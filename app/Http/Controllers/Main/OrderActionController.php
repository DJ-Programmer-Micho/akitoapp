<?php

namespace App\Http\Controllers\Main;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Transaction;
use App\Models\User;
use App\Services\WalletService;
use App\Events\EventOrderStatusUpdated;
use App\Mail\EmailInvoiceActionFailedMail;
use App\Notifications\NotifyOrderStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OrderActionController extends Controller
{
    public function cancelOrder(Request $request, string $locale, int $orderId)
    {
        try {
            $customer = Auth::guard('customer')->user();
            if (!$customer) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }

            $reason = $request->input('reason', 'User Cancelled');

            DB::transaction(function () use ($orderId, $customer, $reason) {
                // Lock order row for concurrency safety
                $order = Order::with('orderItems', 'customer.wallet')
                    ->lockForUpdate()
                    ->where('id', $orderId)
                    ->where('customer_id', $customer->id)
                    ->first();

                if (!$order) {
                    abort(404, 'Order not found');
                }

                // Already terminal
                if (in_array($order->status, ['cancelled', 'refunded'], true)) {
                    abort(422, 'Order already cancelled or refunded.');
                }

                // Latest transaction (if any) → mark failed if still pending
                $transaction = Transaction::where('order_id', $order->id)
                    ->orderByDesc('created_at')
                    ->first();

                if ($transaction && $transaction->status === 'pending') {
                    $existingResponse = $transaction->response;

                    if (!is_array($existingResponse)) {
                        $decoded = json_decode($existingResponse, true);
                        $existingResponse = is_array($decoded) ? $decoded : (array) $existingResponse;
                    }

                    $transaction->update([
                        'status'   => 'failed',
                        'response' => array_merge($existingResponse, [
                            'cancel_reason' => $reason,
                            'by'            => 'customer',
                        ]),
                    ]);
                }

                // ---------------------------------
                // Detect "paid" vs "unpaid" safely
                // ---------------------------------
                $isPaidStatus = in_array($order->payment_status, ['successful', 'paid'], true);

                // Prefer paid_minor if set, otherwise fall back to total
                $paidMinor = (int) ($order->paid_minor ?? 0);
                if ($paidMinor <= 0 && $isPaidStatus) {
                    // best-effort fallback to order total in minor units
                    $paidMinor = (int) ($order->total_minor
                        ?? (($order->total_amount_iqd ?? 0) + ($order->shipping_amount ?? 0)));
                }

                // If payment was already captured successfully (or 'paid'), lock refundable amount
                if ($isPaidStatus && $paidMinor > 0) {
                    $itemsSubtotal = (int) $order->orderItems->sum('total_iqd');   // items (IQD minor)
                    $shipping      = (int) ($order->shipping_amount ?? 0);        // shipping (IQD minor)

                    // Exclude gateway fees – only items + shipping
                    $refundableBase   = max(0, $itemsSubtotal + $shipping);
                    $alreadyRefunded  = (int) ($order->refunded_minor ?? 0);
                    $maxRefundable    = max(0, $paidMinor - $alreadyRefunded);
                    $toLock           = max(0, min($refundableBase, $maxRefundable));

                    if ($toLock > 0) {
                        $wallet = $order->customer->wallet()
                            ->lockForUpdate()
                            ->firstOrCreate(['currency' => 'IQD']);

                        app(\App\Services\WalletService::class)->lock($wallet, $toLock, [
                            'reason' => 'refund_lock',
                            'meta'   => [
                                'order_id'  => $order->id,
                                'tracking'  => $order->tracking_number,
                                'by'        => 'customer',
                                'note'      => $reason,
                                'items_iqd' => $itemsSubtotal,
                                'shipping'  => $shipping,
                            ],
                        ]);
                    }

                    // Keep payment_status as "successful" / "paid"
                    // Use locked_minor + UI "Refund Pending" to show state to customer
                    $order->status = 'cancelled';
                    $order->save();
                } else {
                    // Not successfully paid yet → normal failed + cancelled
                    $order->payment_status = 'failed';
                    $order->status         = 'cancelled';
                    $order->save();
                }

                // Send email to customer about cancellation
                try {
                    if ($order->customer && $order->customer->email) {
                        Mail::to($order->customer->email)->queue(new EmailInvoiceActionFailedMail($order));
                    }
                } catch (\Throwable $e) {
                    Log::warning('Email send failed on customer cancel: ' . $e->getMessage());
                }

                // Notify admins & broadcast
                try {
                    broadcast(new EventOrderStatusUpdated(
                        $order->tracking_number,
                        $order->id,
                        $order->status
                    ))->toOthers();
                } catch (\Throwable $e) {
                    Log::info('Broadcast failed on customer cancel: ' . $e->getMessage());
                }

                $adminUsers = User::whereHas('roles', function ($q) {
                        $q->whereIn('name', [
                            'Administrator',
                            'Data Entry Specialist',
                            'Finance Manager',
                            'Order Processor',
                        ]);
                    })
                    ->whereDoesntHave('roles', function ($q) {
                        $q->where('name', 'Driver');
                    })
                    ->get();

                foreach ($adminUsers as $admin) {
                    if (!$admin->notifications()
                        ->where('data->order_id', $order->tracking_number)
                        ->where('data->status', $order->status)
                        ->exists()) {
                        $admin->notify(new NotifyOrderStatusChanged(
                            $order->tracking_number,
                            $order->id,
                            $order->status,
                            "Order ID {$order->tracking_number} has been updated to {$order->status} (customer-cancelled)."
                        ));
                    }
                }
            });

            Log::info('Customer cancelled order', [
                'order_id' => $orderId,
                'customer' => $customer->id,
            ]);

            return response()->json([
                'message' => 'Order cancelled successfully. If payment was captured, refund is now pending in your wallet.',
            ]);
        } catch (\Symfony\Component\HttpKernel\Exception\HttpException $e) {
            return response()->json(['error' => $e->getMessage()], $e->getStatusCode());
        } catch (\Throwable $e) {
            Log::error('Error in customer cancelOrder: ' . $e->getMessage(), [
                'order_id' => $orderId ?? null,
            ]);
            return response()->json(['error' => 'Internal server error'], 500);
        }
    }

}
