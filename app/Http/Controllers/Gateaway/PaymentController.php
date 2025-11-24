<?php

namespace App\Http\Controllers\Gateaway;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\Transaction;
use App\Models\Payment;
use App\Models\PaymentMethods;
use App\Models\Customer;
use App\Services\PaymentServiceManager;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    //////////////////////////////
    // REDIRECT PAYMENTS
    //////////////////////////////

    public function processFrontPayment($locale, $orderId, $paymentId, $paymentMethodId)
    {
        $paymentMethod = PaymentMethods::findOrFail($paymentMethodId);
        $methodName    = $paymentMethod->name;

        // orderId = 0 => wallet topup (no order)
        $order = null;
        if ((int)$orderId !== 0) {
            $order = Order::findOrFail($orderId);
        }

        if ($methodName === 'FIB') {
            return redirect()->route('payment.fib', [
                'locale'    => app()->getLocale(),
                'paymentId' => $paymentId,
            ]);
        } elseif ($methodName === 'Areeba') {
            if ($order) {
                return redirect()->route('payment.areeba', [
                    'locale'    => app()->getLocale(),
                    'paymentId' => $order->id,
                ]);
            }
        } elseif ($methodName === 'Stripe') {
            if ($order) {
                return $this->redirectToStripeCheckout($order, $paymentId);
            }
        } elseif ($methodName === 'ZainCash') {
            if ($order) {
                return redirect()->route('payment.zaincash', [
                    'locale'    => app()->getLocale(),
                    'paymentId' => $order->id,
                ]);
            }
        }

        return redirect()->route('business.checkout.failed')
            ->withErrors('Invalid payment provider or missing order.');
    }

    //////////////////////////////
    // FIB ROUTES
    //////////////////////////////

    public function showFIBPaymentPage($locale, $paymentId)
    {
        $qrCode        = session('qrCode');
        $readableCode  = session('readableCode');
        $personalAppLink = session('personalAppLink');

        if (!$qrCode || !$personalAppLink || !$readableCode) {
            return redirect()->route('business.checkout.failed')
                ->withErrors('Payment session expired.');
        }

        return view('mains.payments-page.fib.fib-payment', compact(
            'paymentId',
            'qrCode',
            'personalAppLink',
            'readableCode'
        ));
    }

    public function checkFIBPaymentStatus($locale, $paymentId, $paymentMethod)
    {
        try {
            $paymentService = PaymentServiceManager::getInstance();
            $statusData = $paymentService->fetchFIBPaymentStatus($paymentId);

            if (!$statusData) {
                return response()->json(['error' => 'Failed to check payment status'], 500);
            }

            // 1) Update transaction row (if exists)
            $transaction = Transaction::where('id', $paymentId)->first();
            if ($transaction) {
                $transaction->update([
                    'status'   => $statusData['status'] ?? $transaction->status,
                    'response' => $statusData,
                ]);
            }

            // 2) Try find payment using provider_payment_id
            $payment = Payment::where('provider_payment_id', $paymentId)
                ->where('provider', 'FIB')
                ->first();

            // 2b) Fallback: use transaction.payment_id if mapping exists
            if (!$payment && $transaction && !empty($transaction->payment_id)) {
                $payment = Payment::find($transaction->payment_id);
            }

            if (!$payment) {
                Log::warning('FIB status: payment row not found', [
                    'fib_payment_id' => $paymentId,
                ]);
            }

            // ---------- PAID ----------
            if (($statusData['status'] ?? null) === 'PAID') {

                // (a) ORDER PAYMENT FLOW
                if ($payment && $payment->order_id) {
                    $order = Order::find($payment->order_id);

                    if ($order) {
                        $order->update([
                            'payment_status' => 'successful',
                            'payment_method' => $paymentMethod,
                        ]);

                        CartItem::where('customer_id', $order->customer_id)->delete();
                    }

                    $payment->update(['status' => 'successful']);
                }

                // (b) WALLET TOP-UP FLOW
                if ($payment && is_null($payment->order_id)) {
                    $meta = is_array($payment->meta) ? $payment->meta : [];

                    if (($meta['kind'] ?? null) === 'wallet_topup') {
                        $customerId = $meta['customer_id'] ?? null;
                        $topupMinor = (int) ($meta['topup_minor'] ?? 0);
                        $feeMinor   = (int) ($meta['fee_minor'] ?? 0);

                        if ($customerId && $topupMinor > 0) {
                            $customer = Customer::find($customerId);

                            if ($customer) {
                                $wallet = $customer->wallet()
                                    ->lockForUpdate()
                                    ->firstOrCreate(['currency' => 'IQD']);

                                app(WalletService::class)->credit($wallet, $topupMinor, [
                                    'reason' => 'wallet_topup',
                                    'meta'   => [
                                        'provider'   => 'FIB',
                                        'payment_id' => $payment->id,
                                        'fee_minor'  => $feeMinor,
                                        'fib_id'     => $paymentId,
                                    ],
                                ]);

                                $payment->update(['status' => 'successful']);
                            } else {
                                Log::error('FIB wallet topup: customer not found', [
                                    'customer_id' => $customerId,
                                ]);
                            }
                        } else {
                            Log::error('FIB wallet topup: invalid meta on payment', [
                                'payment_id' => $payment->id ?? null,
                                'meta'       => $meta,
                            ]);
                        }
                    }
                }

                return response()->json($statusData);
            }

            // ---------- DECLINED ----------
            if (($statusData['status'] ?? null) === 'DECLINED') {
                if ($payment) {
                    $payment->update(['status' => 'failed']);
                }

                if ($transaction && $transaction->order_id) {
                    Order::where('id', $transaction->order_id)
                        ->update(['payment_status' => 'failed']);
                }

                return response()->json($statusData);
            }

            // Any other status – just return it
            return response()->json($statusData);

        } catch (\Exception $e) {
            Log::error("FIB Payment Status Error: " . $e->getMessage(), [
                'fib_payment_id' => $paymentId,
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
    

    public function cancelPayment($paymentId, Request $request)
    {
        try {
            $transaction = Transaction::where('id', $paymentId)->first();

            if (!$transaction || $transaction->status !== 'pending') {
                return response()->json(['error' => '❌ Transaction not found or already processed'], 404);
            }

            $transaction->update([
                'status'   => 'declined',
                'response' => ['reason' => $request->input('reason', 'Timeout')],
            ]);

            if ($transaction->order_id) {
                Order::where('id', $transaction->order_id)->update([
                    'payment_status' => 'failed',
                ]);
            }

            if ($transaction->payment) {
                $transaction->payment->update(['status' => 'failed']);
            }

            return response()->json(['message' => '🚫 Payment marked as declined']);
        } catch (\Exception $e) {
            return response()->json(['error' => '⚠️ Internal server error 0'], 500);
        }
    }

    //////////////////////////////
    // STRIPE REDIRECT
    //////////////////////////////

    private function redirectToStripeCheckout($order, $sessionId)
    {
        $transaction = Transaction::where('stripe_session_id', $sessionId)
            ->where('order_id', $order->id)
            ->first();

        if (!$transaction) {
            Log::error("Stripe Checkout Error: No transaction found matching sessionId={$sessionId} and order_id={$order->id}");
            return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                ->withErrors('Transaction not found or expired.');
        }

        $responseData = $transaction->response;

        if (!isset($responseData['checkout_url'])) {
            Log::error("Stripe Checkout Error: Missing 'checkout_url' in transaction->response for sessionId={$sessionId}");
            return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                ->withErrors('Missing Stripe Checkout URL in transaction.');
        }

        $checkoutUrl = $responseData['checkout_url'];

        Log::info("Redirecting to Stripe Checkout for sessionId={$sessionId}, order_id={$order->id}");
        return redirect($checkoutUrl);
    }

    public function handleStripeWebhook(Request $request)
    {
        // TODO:
        // 1) Parse the event from Stripe
        // 2) If event type is checkout.session.completed
        // 3) Mark transaction as 'paid' if payment_status == 'paid'
        // 4) Mark the order as 'successful' and payment row as 'successful'
    }

    //////////////////////////////
    // DIGIT STATUS PAGES
    //////////////////////////////

    public function digitSuccess()
    {
        $isLoggedIn = Auth::guard('customer')->check();

        if (!$isLoggedIn) {
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return view('mains.payments-page.status.digit-sucess');
    }

    public function digitCancel()
    {
        $isLoggedIn = Auth::guard('customer')->check();

        if (!$isLoggedIn) {
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return view('mains.payments-page.status.digit-cancel');
    }

    public function digitError()
    {
        $isLoggedIn = Auth::guard('customer')->check();

        if (!$isLoggedIn) {
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return view('mains.payments-page.status.digit-error');
    }
}
