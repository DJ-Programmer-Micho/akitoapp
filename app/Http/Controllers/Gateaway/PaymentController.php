<?php

namespace App\Http\Controllers\Gateaway;

use App\Models\Order;
use App\Models\CartItem;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\PaymentMethods;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use App\Services\PaymentServiceManager;

class PaymentController extends Controller
{

    //////////////////////////////
    // REDIRECT PAYMENTS AND INTERACT WITH DB
    //////////////////////////////

    // MAIN FUNCTION
    public function processFrontPayment($locale, $orderId, $paymentId, $paymentMethod)
    {
        $order = Order::findOrFail($orderId);
        $getPaymentMethod = PaymentMethods::where('id',$paymentMethod)->first();
        $paymentMethod = $getPaymentMethod->name;

        if ($paymentMethod == 'FIB') {
            return redirect()->route('payment.fib', ['locale' => app()->getLocale(), 'paymentId' => $paymentId]);
        } elseif ($paymentMethod == 'Areeba') {
            return redirect()->route('payment.areeba', ['locale' => app()->getLocale(), 'paymentId' => $order->id]);
        } elseif ($paymentMethod == 'ZainCash') {
            return redirect()->route('payment.zaincash', ['locale' => app()->getLocale(), 'paymentId' => $order->id]);
        } else {
            return redirect()->route('business.checkout.failed')->withErrors('Invalid payment provider.');
        }
    }


    //////////////////////////////
    // FIB ROUTES
    //////////////////////////////
    public function showFIBPaymentPage($locale, $paymentId)
    {
        $qrCode = session('qrCode');
        $readableCode = session('readableCode');
        $personalAppLink = session('personalAppLink');
    
        if (!$qrCode || !$personalAppLink || !$readableCode) {
            return redirect()->route('business.checkout.failed')
                ->withErrors('Payment session expired.');
        }
    
        return view('mains.payments-page.fib.fib-payment', compact('paymentId', 'qrCode', 'personalAppLink', 'readableCode'));
    }

    public function checkFIBPaymentStatus($locale, $paymentId, $paymentMethod)
    {
        try {
            $paymentService = PaymentServiceManager::getInstance();
            $statusData = $paymentService->fetchFIBPaymentStatus($paymentId); // Securely fetch status

            if (!$statusData) {
                return response()->json(['error' => 'Failed to check payment status aa'], 500);
            }

            // Log::info("FIB Payment Status Response: ", $statusData);

            $transaction = Transaction::where('id', $paymentId)->first();
            if ($transaction) {
                if ($statusData['status'] === 'PAID') {
                    $transaction->update(['status' => 'paid']);
                    $transaction->update(['response' => $statusData]);
                    Order::where('id', $transaction->order_id)->update([
                        'payment_status' => 'successful',
                        'payment_method' => $paymentMethod,
                    ]);
                    CartItem::where('customer_id', $transaction->order->customer_id)->delete();
                } elseif ($statusData['status'] === 'DECLINED') {
                    $transaction->update(['status' => 'declined']);
                }
            }

            return response()->json($statusData);
        } catch (\Exception $e) {
            Log::error("FIB Payment Status Error: " . $e->getMessage());
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
    
            // ✅ Update transaction status to "Declined"
            $transaction->update([
                'status' => 'declined',
                'response' => ['reason' => $request->input('reason', 'Timeout')],
            ]);
    
            // ✅ Update related order's payment status
            Order::where('id', $transaction->order_id)->update([
                'payment_status' => 'failed',
            ]);
            // Log::info("❌ Payment {$paymentId} declined due to timeout.");
            return response()->json(['message' => '🚫 Payment marked as declined']);
        } catch (\Exception $e) {
            // Log::error("❌ Error canceling payment: " . $e->getMessage());
            return response()->json(['error' => '⚠️ Internal server error 0'], 500);
        }
    }

    
    //////////////////////////////
    // PROCESS AREEBA PAYMENT AND INTERACT WITH DB
    //////////////////////////////

    
    //////////////////////////////
    // PROCESS ZAINCASH PAYMENT AND INTERACT WITH DB
    //////////////////////////////
    public function digitSuccess(){
        $isLoggedIn = Auth::guard('customer')->check();

        if(!$isLoggedIn){
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return view('mains.payments-page.status.digit-sucess');
    }

    public function digitCancel(){
        $isLoggedIn = Auth::guard('customer')->check();

        if(!$isLoggedIn){
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return view('mains.payments-page.status.digit-cancel');
    }

    public function digitError(){
        $isLoggedIn = Auth::guard('customer')->check();

        if(!$isLoggedIn){
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return view('mains.payments-page.status.digit-error');
    }
}