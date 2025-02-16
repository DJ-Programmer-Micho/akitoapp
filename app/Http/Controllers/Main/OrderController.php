<?php

namespace App\Http\Controllers\Main;

use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;

class OrderController extends Controller
{
    public function cancelOrder($orderId, Request $request)
    {
        try {
            $transaction = Transaction::where('order_id', $orderId)->first();
            // if (!$transaction || $transaction->status !== 'pending') {
            //     return response()->json(['error' => '❌ Transaction not found or already processed'], 404);
            // }
            Log::info('declined');
            
            // ✅ Update transaction status to "Declined"
            $transaction->update([
                'status' => 'declined',
                'response' => ['reason' => $request->input('reason', 'Timeout')],
            ]);
    
            // ✅ Update related order's payment status
            Order::where('id', $orderId)->update([
                'payment_status' => 'failed',
                'status' => 'canceled'
            ]);
            // Log::info("❌ Payment {$paymentId} declined due to timeout.");
            return response()->json(['message' => '🚫 Payment marked as declined']);
        } catch (\Exception $e) {
            // Log::error("❌ Error canceling payment: " . $e->getMessage());
            return response()->json(['error' => '⚠️ Internal server error'], 500);
        }
    }
}
