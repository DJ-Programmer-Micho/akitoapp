<?php

namespace App\Http\Controllers\Gateaway;

use App\Models\Plan;
use App\Models\User;
use App\Models\Order;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\PlanChange;
use App\Models\Transaction;
use App\Models\Subscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PaymentServiceManager;
use Illuminate\Support\Facades\Notification;
use App\Notifications\Owner\TelegramPlanChangeNew;

class CallBackController extends Controller
{
        // $data = [
        //     "result" => "OK",
        //     "uuid" => "fb47ff137684df861dc4",
        //     "merchantTransactionId" => "da358214-adc7-4e1c-9f08-a0a99d473ed0",
        //     "purchaseId" => "20231012-fb47ff137684df861dc4",
        //     "transactionType" => "DEBIT",
        //     "paymentMethod" => "Creditcard",
        //     "amount" => "50.00",
        //     "currency" => "USD",
        //     "customer" => [
        //         "firstName" => "Michel Shabo",
        //         "lastName" => "N/A",
        //         "company" => "minemenu",
        //         "ipAddress" => "127.0.0.1",
        //     ],
        //     "returnData" => [
        //         "_TYPE" => "cardData",
        //         "type" => "mastercard",
        //         "cardHolder" => "areeba",
        //         "expiryMonth" => "05",
        //         "expiryYear" => "2026",
        //         "binDigits" => "51234500",
        //         "firstSixDigits" => "512345",
        //         "lastFourDigits" => "0008",
        //         "fingerprint" => "/9NMen+1D5cGfQUB5NHb+mDrnqCBeL86wdGbuzCf7avMpvlMZEBJr1xBrZyAPTH02cJ6+Yz3O61kN+5MugQjNQ",
        //         "threeDSecure" => "OPTIONAL",
        //         "eci" => "02",
        //         "binBrand" => "MASTERCARD",
        //         "binBank" => "Afriland First Bank",
        //         "binType" => "CREDIT",
        //         "binLevel" => "STANDARD",
        //         "binCountry" => "LR",
        //     ],
        // ];

    //////////////////////////////
    // HANDLE CALLBACKS FROM PROVIDERS
    //////////////////////////////
    public function handleCallback(Request $request, $provider)
    {
        // Log::info("Received Callback for: {$provider}", $request->all());

        switch (strtolower($provider)) {
            case 'areeba':
                return $this->areebaCallBack($request);
            case 'zaincash':
                return $this->zainCashCallBack($request);
            case 'fib':
                return $this->fibCallback($request);
            default:
                return response()->json(['error' => 'Invalid provider'], 400);
        }
    }

    //////////////////////////////
    // HANDLE AREEBA CALLBACK
    //////////////////////////////
    private function areebaCallBack(Request $request)
    {
        $data = $request->all();
        // Log::info("Areeba Callback Data: ", $data);

        $transaction = Transaction::where('transaction_id', $data['merchantTransactionId'])->first();
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $order = Order::find($transaction->order_id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $transaction->update([
            'status' => $data['result'] === 'OK' ? 'completed' : 'failed',
            'response' => $data,
        ]);

        if ($data['result'] === 'OK') {
            $order->update(['payment_status' => 'paid']);
            return redirect()->route('payment.success');
        } else {
            return redirect()->route('payment.error');
        }
    }
    
    //////////////////////////////
    // HANDLE ZAINCASH CALLBACK
    //////////////////////////////
    private function zainCashCallBack(Request $request)
    {
        if (!$request->has('token')) {
            return redirect()->route('payment.cancel');
        }

        $result = JWT::decode($request->token, new Key(env('ZAINCASH_SECRET_KEY'), 'HS256'));
        $data = (array) $result;

        // Log::info("ZainCash Callback Data: ", $data);

        $transaction = Transaction::where('transaction_id', $data['orderid'])->first();
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $order = Order::find($transaction->order_id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $transaction->update([
            'status' => $data['status'] === 'success' ? 'completed' : 'failed',
            'response' => $data,
        ]);

        if ($data['status'] === 'success') {
            $order->update(['payment_status' => 'paid']);
            return redirect()->route('payment.success');
        } else {
            return redirect()->route('payment.error');
        }
    }
    
    //////////////////////////////
    // HANDLE FIB CALLBACK
    //////////////////////////////
    // THE REQUEST
    // https://webhook-test.com/payload/0984de67-adb9-4715-9f9f-c41730d3261d
    // {
    //     "id": "d2351c43-187c-40d3-a2fb-5c72300b3737",
    //     "status": "PAID"
    // }
    public function fibCallback($paymentId)
    {
        try {
            // ✅ Call PaymentServiceManager method securely
            $paymentService = PaymentServiceManager::getInstance();
            $statusData = $paymentService->fetchFIBPaymentStatus($paymentId);

            if (!$statusData) {
                return response()->json(['error' => 'Failed to check payment status'], 500);
            }

            // Log::info("FIB Payment Status Response: ", $statusData);

            $transaction = Transaction::where('id', $paymentId)->first();
            if ($transaction) {
                if ($statusData['status'] === 'PAID') {
                    $transaction->update(['status' => 'paid']);
                    $transaction->update(['response' => $statusData]);
                    Order::where('id', $transaction->order_id)->update(['payment_status' => 'successful']);
                } elseif ($statusData['status'] === 'DECLINED') {
                    $transaction->update(['status' => 'declined']);
                }
            }

            return response()->json($statusData);
        } catch (\Exception $e) {
            // Log::error("FIB Payment Status Error: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
