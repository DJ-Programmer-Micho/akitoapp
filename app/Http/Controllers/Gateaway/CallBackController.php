<?php

namespace App\Http\Controllers\Gateaway;

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Services\PaymentServiceManager;

class CallBackController extends Controller
{
    // AREEBA WEBHOOK CALL BACK EXAMPLE:
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

        $transaction = Transaction::where('transaction_id', $data['merchantTransactionId'])->first();
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $order = Order::find($transaction->order_id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $transaction->update([
            'status'   => $data['result'] === 'OK' ? 'completed' : 'failed',
            'response' => $data,
        ]);

        if ($data['result'] === 'OK') {
            $order->update(['payment_status' => 'successful']);
            return redirect()->route('digit.payment.success', ['locale' => app()->getLocale()]);
        } else {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('digit.payment.error', ['locale' => app()->getLocale()]);
        }
    }

    //////////////////////////////
    // HANDLE ZAINCASH CALLBACK
    //////////////////////////////
    private function zainCashCallBack(Request $request)
    {
        if (!$request->has('token')) {
            return redirect()->route('digit.payment.cancel', ['locale' => app()->getLocale()]);
        }

        $result = JWT::decode($request->token, new Key(env('ZAINCASH_SECRET_KEY'), 'HS256'));
        $data = (array) $result;

        $transaction = Transaction::where('transaction_id', $data['orderid'])->first();
        if (!$transaction) {
            return response()->json(['error' => 'Transaction not found'], 404);
        }

        $order = Order::find($transaction->order_id);
        if (!$order) {
            return response()->json(['error' => 'Order not found'], 404);
        }

        $transaction->update([
            'status'   => $data['status'] === 'success' ? 'completed' : 'failed',
            'response' => $data,
        ]);

        if ($data['status'] === 'success') {
            $order->update(['payment_status' => 'successful']);
            return redirect()->route('digit.payment.success', ['locale' => app()->getLocale()]);
        } else {
            $order->update(['payment_status' => 'failed']);
            return redirect()->route('digit.payment.error', ['locale' => app()->getLocale()]);
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
    public function fibCallback(Request $request)
    {
        try {
            $payload = $request->all();
            // Log::info('FIB callback raw payload', $payload);

            // The FIB "id" here is the same as the paymentId we stored in Transaction::id
            $paymentId = $payload['id'] ?? $payload['paymentId'] ?? null;
            if (!$paymentId) {
                return response()->json(['error' => 'Missing paymentId/id'], 400);
            }

            // Ask FIB for definitive status using the API wrapper
            $paymentService = PaymentServiceManager::getInstance();
            $statusData     = $paymentService->fetchFIBPaymentStatus($paymentId);

            if (!$statusData) {
                Log::error('FIB fetch status failed', ['paymentId' => $paymentId]);
                return response()->json(['error' => 'Failed to check payment status'], 500);
            }

            // Log::info('FIB Payment Status Response', $statusData);

            $transaction = Transaction::where('id', $paymentId)
                ->where('provider', 'FIB')
                ->first();

            if (!$transaction) {
                return response()->json(['error' => 'Transaction not found'], 404);
            }

            $order   = $transaction->order;
            $payment = $transaction->payment;

            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            DB::beginTransaction();

            // Depending on FIB docs, status may be: PAID, COMPLETED, DECLINED, CANCELLED ...
            $fibStatus = strtoupper($statusData['status'] ?? '');

            $isPaid = in_array($fibStatus, ['PAID', 'COMPLETED'], true);
            $isDeclined = in_array($fibStatus, ['DECLINED', 'CANCELLED', 'FAILED'], true);

            if ($isPaid) {
                $transaction->status   = 'successful';
                $transaction->response = $statusData;
                $transaction->save();

                if ($payment) {
                    $payment->status              = 'successful';
                    $payment->provider_payment_id = $statusData['paymentId'] ?? $paymentId;
                    $payment->save();

                    if ((int) $payment->amount_minor > 0) {
                        $order->paid_minor = (int) ($order->paid_minor ?? 0) + (int) $payment->amount_minor;
                    }
                }

                $order->payment_status = 'successful';
                $order->save();
            } elseif ($isDeclined) {
                $transaction->status   = 'failed';
                $transaction->response = $statusData;
                $transaction->save();

                if ($payment) {
                    $payment->status = 'failed';
                    $payment->save();
                }

                if ($order->payment_status === 'pending') {
                    $order->payment_status = 'failed';
                    $order->save();
                }
            } else {
                // Unknown / intermediate status, just store it
                $transaction->status   = strtolower($fibStatus) ?: 'pending';
                $transaction->response = $statusData;
                $transaction->save();
            }

            DB::commit();

            // For FIB callback you usually just respond JSON (not redirect)
            return response()->json(['ok' => true, 'status' => $fibStatus]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("FIB Payment Status Error: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
