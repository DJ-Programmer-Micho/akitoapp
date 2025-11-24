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
            $transaction = Transaction::where('order_id', $orderId)->latest()->first();
            if ($transaction) {
                $transaction->update([
                    'status'   => 'failed', // 'declined' → align to 'failed'
                    'response' => ['reason' => $request->input('reason', 'Timeout')],
                ]);
            }

            $order = Order::find($orderId);
            if (!$order) {
                return response()->json(['error' => 'Order not found'], 404);
            }

            // If we are here due to gateway timeout BEFORE success, it's a failure.
            $order->payment_status = 'failed';
            $order->status         = 'cancelled';
            $order->save();

            Mail::to($order->customer->email)->queue(new EmailInvoiceActionFailedMail($order));

            try {
                broadcast(new EventOrderStatusUpdated($order->tracking_number, $order->id, $order->status))->toOthers();
                $adminUsers = User::whereHas('roles', function ($q) {
                    $q->whereIn('name', ['Administrator','Data Entry Specialist','Finance Manager','Order Processor']);
                })->whereDoesntHave('roles', function ($q) {
                    $q->where('name', 'Driver');
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
            } catch (\Throwable $e) {
                // swallow broadcasting failure; return success to client
            }

            Log::info("Payment attempt failed/timeout; order cancelled.", ['order' => $orderId]);
            return response()->json(['message' => 'Payment marked as failed & order cancelled']);
        } catch (\Throwable $e) {
            Log::error("Error canceling payment: " . $e->getMessage());
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }
}
