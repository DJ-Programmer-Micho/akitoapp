<?php

namespace App\Http\Controllers\Main;

use App\Models\User;
use App\Models\Order;
use App\Models\Transaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Mail;
use App\Events\EventOrderStatusUpdated;
use App\Mail\EmailInvoiceActionFailedMail;
use App\Notifications\NotifyOrderStatusChanged;

class OrderController extends Controller
{
    public function cancelOrder($orderId, Request $request)
    {
        try {
            $transaction = Transaction::where('order_id', $orderId)->first();
            // if (!$transaction || $transaction->status !== 'pending') {
            //     return response()->json(['error' => '❌ Transaction not found or already processed'], 404);
            // }            
            // ✅ Update transaction status to "Declined"
            if($transaction){
                $transaction->update([
                    'status' => 'declined',
                    'response' => ['reason' => $request->input('reason', 'Timeout')],
                ]);
            }
    
            // ✅ Update related order's payment status
            $order = Order::findOrFail($orderId);
            if(!$order){
                return response()->json(['error' => 'Transaction not found'], 404);
            }
            $order->payment_status = 'failed';
            $order->status         = 'canceled';
            $order->save();

            Mail::to($order->customer->email)->queue(new EmailInvoiceActionFailedMail($order));

            try {
                broadcast(new EventOrderStatusUpdated($order->tracking_number, $order->id, $order->status))->toOthers();    

                $adminUsers = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Administrator')
                          ->orWhere('name', 'Data Entry Specialist')
                          ->orWhere('name', 'Finance Manager')
                          ->orWhere('name', 'Order Processor');
                })->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Driver');
                })->get();
                
                foreach ($adminUsers as $admin) {
                    if (!$admin->notifications()->where('data->order_id', $order->tracking_number)
                        ->where('data->status', $order->status)->exists()) {
                        $admin->notify(new NotifyOrderStatusChanged(
                            $order->tracking_number, 
                            $order->id,
                            $order->status, 
                            "Order ID {$order->tracking_number} has been updated to {$order->status}", 
                        ));
                    }
                }
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('Your Internet is Weak!: ' . $e->getMessage())]);
                return;
            }

            Log::info("❌ Payment declined due to timeout.");
            return response()->json(['message' => '🚫 Payment marked as declined']);
        } catch (\Exception $e) {
            Log::error("❌ Error canceling payment: " . $e->getMessage());
            return response()->json(['error' => '⚠️ Internal server error'], 500);
        }
    }
}
