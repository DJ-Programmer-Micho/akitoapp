<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class OrderRefundService
{
    public function performRefund(Order $order, string $reason = 'admin_action'): void
    {
        $itemsSubtotal = (int) $order->orderItems->sum('total_iqd');
        $shipping      = (int) ($order->shipping_amount ?? 0);
        $refundableBase = $itemsSubtotal + $shipping;

        $alreadyRefunded = (int) ($order->refunded_minor ?? 0);
        $paid            = (int) ($order->paid_minor ?? 0);
        $maxRefundable   = max(0, $paid - $alreadyRefunded);
        $toRefund        = max(0, min($refundableBase, $maxRefundable));

        if ($toRefund <= 0) {
            throw new \RuntimeException(__('Nothing to refund'));
        }

        DB::transaction(function () use ($order, $toRefund, $itemsSubtotal, $shipping, $reason) {
            $wallet = $order->customer->wallet()->lockForUpdate()->firstOrCreate(['currency' => 'IQD']);

            app(WalletService::class)->credit($wallet, $toRefund, [
                'reason' => $reason,
                'meta'   => [
                    'order_id'  => $order->id,
                    'tracking'  => $order->tracking_number,
                    'items_iqd' => $itemsSubtotal,
                    'shipping'  => $shipping,
                ],
            ]);

            $order->refunded_minor = (int) ($order->refunded_minor ?? 0) + $toRefund;
            $order->payment_status = 'refunded';
            if ($order->status !== 'delivered') {
                $order->status = 'refunded';
            }
            $order->save();

            $payment = Payment::create([
                'order_id'           => $order->id,
                'amount_minor'       => $toRefund,
                'currency'           => 'IQD',
                'method'             => 'Wallet',
                'status'             => 'successful',
                'provider'           => 'Wallet',
                'provider_payment_id'=> null,
                'idempotency_key'    => Str::uuid(),
            ]);

            Transaction::create([
                'id'           => Str::uuid(),
                'payment_id'   => $payment->id,
                'order_id'     => $order->id,
                'provider'     => 'Wallet',
                'amount_minor' => $toRefund,
                'currency'     => 'IQD',
                'status'       => 'successful',
                'response'     => [
                    'kind'   => 'refund',
                    'note'   => 'Admin refund excluding fees',
                    'source' => 'backoffice',
                ],
            ]);
        });
    }

    public function reverseRefund(Order $order, string $reason = 'admin_action'): void
    {
        $itemsSubtotal = (int) $order->orderItems->sum('total_iqd');
        $shipping      = (int) ($order->shipping_amount ?? 0);
        $refunded      = (int) ($order->refunded_minor ?? 0);

        $maxReversible = min($refunded, $itemsSubtotal + $shipping);
        if ($maxReversible <= 0) return;

        DB::transaction(function () use ($order, $maxReversible, $reason) {
            $wallet = $order->customer->wallet()->lockForUpdate()->firstOrCreate(['currency' => 'IQD']);

            if ($wallet->balance_minor < $maxReversible) {
                throw new \RuntimeException(__('Cannot reverse refund: customer wallet balance is less than refundable.'));
            }

            app(WalletService::class)->debit($wallet, $maxReversible, [
                'reason' => $reason,
                'meta'   => ['order_id' => $order->id, 'tracking' => $order->tracking_number],
            ]);

            $order->refunded_minor = (int) ($order->refunded_minor ?? 0) - $maxReversible;
            $order->save();

            $payment = Payment::create([
                'order_id'           => $order->id,
                'amount_minor'       => $maxReversible,
                'currency'           => 'IQD',
                'method'             => 'Wallet',
                'status'             => 'successful',
                'provider'           => 'Wallet',
                'provider_payment_id'=> null,
                'idempotency_key'    => Str::uuid(),
            ]);

            Transaction::create([
                'id'           => Str::uuid(),
                'payment_id'   => $payment->id,
                'order_id'     => $order->id,
                'provider'     => 'Wallet',
                'amount_minor' => $maxReversible,
                'currency'     => 'IQD',
                'status'       => 'successful',
                'response'     => [
                    'kind'   => 'refund_reversal',
                    'note'   => 'Admin switched away from refunded',
                    'source' => 'backoffice',
                ],
            ]);
        });
    }
}
