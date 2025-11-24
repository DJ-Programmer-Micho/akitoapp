<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\PaymentMethods;
use App\Models\Payment;
use App\Services\PaymentServiceManager;
use App\Services\WalletService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WalletTopupController extends Controller
{
    public function showForm($locale)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        // Only online gateways for top-up
        $methods = PaymentMethods::where('active', true)
            ->where('online', 1)
            ->where('name', '!=', 'wallet')
            ->get();

        return view('mains.pages.topup-page-one', [
            'paymentMethods' => $methods,
            'walletBalance'  => (int) ($customer->wallet->balance_minor ?? 0),
        ]);
    }

    public function startTopup(string $locale, Request $request)
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return redirect()->route('business.home', ['locale' => $locale]);
        }

        $validated = $request->validate([
            'amount'  => 'required|integer|min:10000',      // IQD minor (no decimals)
            'payment' => 'required|integer|exists:payment_methods,id',
        ]);

        $method = PaymentMethods::where('id', $validated['payment'])
            ->where('active', 1)
            ->firstOrFail();

        // ❌ Do NOT allow wallet → wallet topup
        if (strtolower($method->name) === 'wallet') {
            return back()->withErrors([
                'payment' => __('This method cannot be used for wallet top-up.'),
            ]);
        }

        // amount credited to wallet (IQD minor)
        $topupMinor = (int) $validated['amount'];

        // dynamic fee from payment_methods.transaction_fee
        $feePercent = (float) ($method->transaction_fee ?? 0);
        $feeMinor   = (int) round($topupMinor * ($feePercent / 100));

        // total we charge at gateway
        $chargeMinor = $topupMinor + $feeMinor;

        try {
            DB::beginTransaction();

            // Create payment row for this top-up (no order_id)
            $payment = Payment::create([
                'order_id'           => null,
                'amount_minor'       => $chargeMinor,
                'currency'           => 'IQD',
                'method'             => $method->name,
                'status'             => 'pending',
                'provider'           => $method->name,
                'provider_payment_id'=> null,                 // filled after gateway init
                'idempotency_key'    => (string) Str::uuid(),
                'meta'               => [
                    'kind'        => 'wallet_topup',
                    'topup_minor' => $topupMinor,
                    'fee_minor'   => $feeMinor,
                    'fee_percent' => $feePercent,
                    'customer_id' => $customer->id,
                ],
            ]);

            // Call your gateway in "topup" mode
            $resp = PaymentServiceManager::getInstance()
                ->setTopupContext($customer, $payment)
                ->setAmount($chargeMinor)            // IQD minor
                ->setPaymentMethod($method->name)
                ->setPaymentId($payment->id)        // so FIB/Areeba can link back if needed
                ->processPayment();

            if (!$resp || empty($resp['paymentId'])) {
                DB::rollBack();
                Log::error('Wallet topup: gateway init failed', [
                    'customer_id' => $customer->id,
                    'method'      => $method->name,
                ]);
                return back()->withErrors([
                    'error' => __('Unable to initialize payment.'),
                ]);
            }

            // 🔑 store provider payment id so callback can find the Payment row
            $payment->update([
                'provider_payment_id' => $resp['paymentId'],
            ]);

            DB::commit();

            // Same redirect used in order checkout
            return redirect()->route('payment.process', [
                'locale'        => $locale,
                'orderId'       => 0, // no order
                'paymentId'     => $resp['paymentId'],
                'paymentMethod' => $method->id,
            ]);
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Wallet topup error: ' . $e->getMessage(), [
                'customer_id' => $customer->id,
            ]);

            return back()->withErrors([
                'error' => __('Unexpected error during top-up.'),
            ]);
        }
    }
}
