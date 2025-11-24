<?php

namespace App\Services;

use App\Models\CustomerWallet;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;

class WalletService
{
    /** Validate amount and normalize meta */
    private function guardAmount(int $amountMinor): void
    {
        if ($amountMinor <= 0) {
            throw new \InvalidArgumentException('Amount must be > 0 (minor units).');
        }
    }

    /** Increases spendable funds (e.g., top-up, approved refund). */
    public function credit(CustomerWallet $wallet, int $amountMinor, array $meta = []): void
    {
        $this->guardAmount($amountMinor);

        DB::transaction(function () use ($wallet, $amountMinor, $meta) {
            // lock row
            $wallet = CustomerWallet::whereKey($wallet->id)->lockForUpdate()->first();
            $wallet->balance_minor += $amountMinor;
            $wallet->version++;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'             => $wallet->id,
                'direction'             => 'credit',
                'amount_minor'          => $amountMinor,
                'currency'              => $wallet->currency,
                'reason'                => $meta['reason']   ?? 'manual_topup',
                'meta'                  => $meta['meta']     ?? null,
                'balance_after_minor'   => $wallet->balance_minor,
                'idempotency_key'       => $meta['idempotency_key'] ?? null,
            ]);
        });
    }

    /** Decreases spendable funds (e.g., pay with wallet). */
    public function debit(CustomerWallet $wallet, int $amountMinor, array $meta = []): void
    {
        $this->guardAmount($amountMinor);

        DB::transaction(function () use ($wallet, $amountMinor, $meta) {
            $wallet = CustomerWallet::whereKey($wallet->id)->lockForUpdate()->first();

            if ($wallet->balance_minor < $amountMinor) {
                throw new \RuntimeException('Insufficient balance.');
            }

            $wallet->balance_minor -= $amountMinor;
            $wallet->version++;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'             => $wallet->id,
                'direction'             => 'debit',
                'amount_minor'          => $amountMinor,
                'currency'              => $wallet->currency,
                'reason'                => $meta['reason']   ?? 'wallet_payment',
                'meta'                  => $meta['meta']     ?? null,
                'balance_after_minor'   => $wallet->balance_minor,
                'idempotency_key'       => $meta['idempotency_key'] ?? null,
            ]);
        });
    }

    /** Reserves funds after a digital payment is cancelled (pending finance). */
    public function lock(CustomerWallet $wallet, int $amountMinor, array $meta = []): void
    {
        $this->guardAmount($amountMinor);

        DB::transaction(function () use ($wallet, $amountMinor, $meta) {
            $wallet = CustomerWallet::whereKey($wallet->id)->lockForUpdate()->first();

            $wallet->locked_minor += $amountMinor;   // reserve
            $wallet->version++;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'             => $wallet->id,
                'direction'             => 'credit',   // ledger says a claim in your favor
                'amount_minor'          => $amountMinor,
                'currency'              => $wallet->currency,
                'reason'                => $meta['reason']   ?? 'refund_lock',
                'meta'                  => $meta['meta']     ?? ['note' => 'Reserved; not spendable'],
                'balance_after_minor'   => $wallet->balance_minor, // balance unchanged
                'idempotency_key'       => $meta['idempotency_key'] ?? null,
            ]);
        });
    }

    /** Approve refund → move from lock to spendable balance. */
    public function releaseToBalance(CustomerWallet $wallet, int $amountMinor, array $meta = []): void
    {
        $this->guardAmount($amountMinor);

        DB::transaction(function () use ($wallet, $amountMinor, $meta) {
            $wallet = CustomerWallet::whereKey($wallet->id)->lockForUpdate()->first();

            if ($wallet->locked_minor < $amountMinor) {
                throw new \RuntimeException('Insufficient locked funds.');
            }

            $wallet->locked_minor  -= $amountMinor;
            $wallet->balance_minor += $amountMinor;
            $wallet->version++;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'             => $wallet->id,
                'direction'             => 'credit',  // spendable balance increased
                'amount_minor'          => $amountMinor,
                'currency'              => $wallet->currency,
                'reason'                => $meta['reason']   ?? 'refund_release',
                'meta'                  => $meta['meta']     ?? ['note' => 'Released from lock'],
                'balance_after_minor'   => $wallet->balance_minor,
                'idempotency_key'       => $meta['idempotency_key'] ?? null,
            ]);
        });
    }

    /** Deny/refuse refund → remove reservation, don’t change spendable balance. */
    public function voidLock(CustomerWallet $wallet, int $amountMinor, array $meta = []): void
    {
        $this->guardAmount($amountMinor);

        DB::transaction(function () use ($wallet, $amountMinor, $meta) {
            $wallet = CustomerWallet::whereKey($wallet->id)->lockForUpdate()->first();

            if ($wallet->locked_minor < $amountMinor) {
                throw new \RuntimeException('Insufficient locked funds.');
            }

            $wallet->locked_minor -= $amountMinor;
            $wallet->version++;
            $wallet->save();

            WalletTransaction::create([
                'wallet_id'             => $wallet->id,
                'direction'             => 'debit',   // claim removed
                'amount_minor'          => $amountMinor,
                'currency'              => $wallet->currency,
                'reason'                => $meta['reason']   ?? 'refund_void',
                'meta'                  => $meta['meta']     ?? ['note' => 'Lock removed'],
                'balance_after_minor'   => $wallet->balance_minor, // balance unchanged
                'idempotency_key'       => $meta['idempotency_key'] ?? null,
            ]);
        });
    }

    /** Bank / external payout: moves funds out of wallet to customer's bank. */
    public function payoutToBank(CustomerWallet $wallet, int $amountMinor, array $meta = []): void
    {
        // We treat it like a normal debit, but with a special reason.
        $meta['reason'] = $meta['reason'] ?? 'payout_to_bank';

        $this->debit($wallet, $amountMinor, $meta);
    }

}
