<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Order;
use Livewire\Component;
use App\Models\Customer;
use Livewire\WithPagination;
use App\Models\CustomerWallet;
use App\Services\WalletService;
use App\Models\WalletTransaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class WalletViewerLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $customerId;

    // filters
    public $tabFilter = 'all'; // all | credit | debit
    public $searchReason = '';
    public $startDate;
    public $endDate;
    public $minAmount;
    public $maxAmount;

    public $manualAmount;
    public $manualType = 'payout'; // 'payout' or 'topup'
    public $manualNote;

    protected $queryString = [
        'tabFilter'    => ['except' => 'all'],
        'searchReason' => ['except' => ''],
        'startDate'    => ['except' => null],
        'endDate'      => ['except' => null],
        'minAmount'    => ['except' => null],
        'maxAmount'    => ['except' => null],
        'page'         => ['except' => 1],
    ];

    public function mount($id)
    {
        $this->customerId = $id;
    }

    public function updatingTabFilter()    { $this->resetPage(); }
    public function updatingSearchReason() { $this->resetPage(); }
    public function updatingStartDate()    { $this->resetPage(); }
    public function updatingEndDate()      { $this->resetPage(); }
    public function updatingMinAmount()    { $this->resetPage(); }
    public function updatingMaxAmount()    { $this->resetPage(); }

    public function changeTab(string $tab)
    {
        $allowed = ['all', 'credit', 'debit'];
        $this->tabFilter = in_array($tab, $allowed, true) ? $tab : 'all';
    }

    public function clearFilters()
    {
        $this->searchReason = '';
        $this->startDate    = null;
        $this->endDate      = null;
        $this->minAmount    = null;
        $this->maxAmount    = null;
        $this->tabFilter    = 'all';
        $this->resetPage();
    }

        /**
     * Approve a locked refund:
     *  - moves amount from locked_minor -> balance_minor
     *  - increments order.refunded_minor
     *  - sets order.payment_status = refunded
     *  - sets order.status = refunded (if not delivered)
     */
    public function approveLock(string $transactionId)
    {
        try {
            DB::transaction(function () use ($transactionId) {
                $wallet = CustomerWallet::where('customer_id', $this->customerId)
                    ->where('currency', 'IQD')
                    ->lockForUpdate()
                    ->firstOrFail();

                $tx = WalletTransaction::where('id', $transactionId)
                    ->where('wallet_id', $wallet->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($tx->direction !== 'credit' || $tx->reason !== 'refund_lock') {
                    throw new \RuntimeException('Invalid transaction type for approval.');
                }

                $amount = (int) $tx->amount_minor;
                $meta   = is_array($tx->meta) ? $tx->meta : (json_decode($tx->meta ?? '[]', true) ?: []);
                $orderId  = $meta['order_id']  ?? null;
                $tracking = $meta['tracking']  ?? null;

                /** @var WalletService $walletService */
                $walletService = app(WalletService::class);

                // Move from lock -> spendable balance
                $walletService->releaseToBalance($wallet, $amount, [
                    'reason' => 'refund_release',
                    'meta'   => [
                        'order_id' => $orderId,
                        'tracking' => $tracking,
                        'note'     => 'Admin approved customer refund',
                    ],
                ]);

                // Update order
                if ($orderId) {
                    $order = Order::lockForUpdate()->find($orderId);
                    if ($order) {
                        $order->refunded_minor = (int) ($order->refunded_minor ?? 0) + $amount;
                        $order->payment_status = 'refunded';

                        if ($order->status !== 'delivered') {
                            $order->status = 'refunded';
                        }

                        $order->save();
                    }
                }
            });

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'success',
                'message' => __('Refund approved and added to wallet balance.'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Approve lock error: ' . $e->getMessage(), [
                'customer_id'    => $this->customerId,
                'transaction_id' => $transactionId,
            ]);

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'error',
                'message' => __('Failed to approve refund: :msg', ['msg' => $e->getMessage()]),
            ]);
        }
    }

    /**
     * Reject a locked refund:
     *  - removes from locked_minor
     *  - does NOT credit wallet balance
     *  - order stays cancelled & paid (business keeps the money).
     */
    public function rejectLock(string $transactionId)
    {
        try {
            DB::transaction(function () use ($transactionId) {
                $wallet = CustomerWallet::where('customer_id', $this->customerId)
                    ->where('currency', 'IQD')
                    ->lockForUpdate()
                    ->firstOrFail();

                $tx = WalletTransaction::where('id', $transactionId)
                    ->where('wallet_id', $wallet->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($tx->direction !== 'credit' || $tx->reason !== 'refund_lock') {
                    throw new \RuntimeException('Invalid transaction type for rejection.');
                }

                $amount = (int) $tx->amount_minor;
                $meta   = is_array($tx->meta) ? $tx->meta : (json_decode($tx->meta ?? '[]', true) ?: []);
                $orderId  = $meta['order_id']  ?? null;
                $tracking = $meta['tracking']  ?? null;

                /** @var WalletService $walletService */
                $walletService = app(WalletService::class);

                // Remove reservation; do not change balance
                $walletService->voidLock($wallet, $amount, [
                    'reason' => 'refund_void',
                    'meta'   => [
                        'order_id' => $orderId,
                        'tracking' => $tracking,
                        'note'     => 'Admin rejected customer refund',
                    ],
                ]);

                // We could also update order to some "refund_rejected" note if you add such field.
            });

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'success',
                'message' => __('Refund lock removed (rejected).'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Reject lock error: ' . $e->getMessage(), [
                'customer_id'    => $this->customerId,
                'transaction_id' => $transactionId,
            ]);

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'error',
                'message' => __('Failed to reject refund: :msg', ['msg' => $e->getMessage()]),
            ]);
        }
    }

        /**
     * Manual admin action on wallet:
     *  - manualType = 'topup'  => credit wallet.balance_minor (manual_topup)
     *  - manualType = 'payout' => debit wallet.balance_minor (payout_to_bank)
     *
     * Does NOT touch locked_minor.
     */
    public function submitManualAction(): void
    {
        try {
            $data = $this->validate([
                'manualAmount' => 'required|numeric|min:1',
                'manualType'   => 'required|in:topup,payout',
                'manualNote'   => 'nullable|string|max:500',
            ]);

            $amountMinor = (int) $data['manualAmount'];
            if ($amountMinor <= 0) {
                throw new \InvalidArgumentException('Amount must be greater than 0.');
            }

            DB::transaction(function () use ($amountMinor, $data) {
                $wallet = CustomerWallet::where('customer_id', $this->customerId)
                    ->where('currency', 'IQD')
                    ->lockForUpdate()
                    ->firstOrFail();

                /** @var WalletService $walletService */
                $walletService = app(WalletService::class);

                $meta = [
                    'reason' => $data['manualType'] === 'topup' ? 'manual_topup' : 'payout_to_bank',
                    'meta'   => [
                        'note'         => $data['manualNote'] ?: null,
                        'by_admin_id'  => auth('web')->id(),
                        'by_admin_type'=> 'manual_action',
                    ],
                ];

                if ($data['manualType'] === 'topup') {
                    // Add to wallet balance
                    $walletService->credit($wallet, $amountMinor, $meta);
                } else {
                    // Deduct from wallet balance (payout to bank)
                    $walletService->payoutToBank($wallet, $amountMinor, $meta);
                }
            });

            // Reset form
            $this->manualAmount = null;
            $this->manualNote   = null;
            $this->manualType   = 'payout';

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'success',
                'message' => $data['manualType'] === 'topup'
                    ? __('Wallet top-up has been added.')
                    : __('Payout to bank has been registered and deducted from wallet.'),
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e; // Livewire will show validation errors
        } catch (\Throwable $e) {
            Log::error('Manual wallet action error: ' . $e->getMessage(), [
                'customer_id' => $this->customerId,
                'type'        => $this->manualType ?? null,
            ]);

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'error',
                'message' => __('Failed to apply wallet action: :msg', ['msg' => $e->getMessage()]),
            ]);
        }
    }

        /**
     * Admin decides to send money back to the customer via bank transfer (outside system).
     *
     * Logic:
     *  - Only uses wallet.balance_minor (never locked_minor).
     *  - Amount must NOT exceed available balance.
     *  - Creates a debit transaction with reason = 'payout_to_bank'.
     *  - Customer still sees full history: refund_release (credit) -> payout_to_bank (debit).
     */
    public function payoutToBank(string $transactionId): void
    {
        try {
            DB::transaction(function () use ($transactionId) {
                // Lock wallet
                $wallet = CustomerWallet::where('customer_id', $this->customerId)
                    ->where('currency', 'IQD')
                    ->lockForUpdate()
                    ->firstOrFail();

                // The source transaction that we base the payout on (usually refund_release / manual_topup)
                $tx = WalletTransaction::where('id', $transactionId)
                    ->where('wallet_id', $wallet->id)
                    ->lockForUpdate()
                    ->firstOrFail();

                if ($tx->direction !== 'credit') {
                    throw new \RuntimeException('Only credit transactions can be paid out.');
                }

                $amount = (int) $tx->amount_minor;

                if ($amount <= 0) {
                    throw new \RuntimeException('Invalid payout amount.');
                }

                if ($wallet->balance_minor < $amount) {
                    throw new \RuntimeException('Insufficient wallet balance for payout.');
                }

                // Decode meta to pass order_id / tracking / note forward
                $meta = is_array($tx->meta)
                    ? $tx->meta
                    : (json_decode($tx->meta ?? '[]', true) ?: []);

                $orderId  = $meta['order_id']  ?? null;
                $tracking = $meta['tracking']  ?? null;

                /** @var WalletService $walletService */
                $walletService = app(WalletService::class);

                // Perform the actual payout (debit).
                $walletService->payoutToBank($wallet, $amount, [
                    'reason' => 'payout_to_bank',
                    'meta'   => [
                        'order_id' => $orderId,
                        'tracking' => $tracking,
                        'note'     => 'Admin bank transfer to customer (offline).',
                        'source_tx_id' => $tx->id,
                    ],
                ]);
            });

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'success',
                'message' => __('Payout to bank has been registered and deducted from wallet.'),
            ]);
        } catch (\Throwable $e) {
            Log::error('Payout to bank error: ' . $e->getMessage(), [
                'customer_id'    => $this->customerId,
                'transaction_id' => $transactionId,
            ]);

            $this->dispatchBrowserEvent('alert', [
                'type'    => 'error',
                'message' => __('Failed to create bank payout: :msg', ['msg' => $e->getMessage()]),
            ]);
        }
    }


    public function render()
    {
        $customer = Customer::with('customer_profile')
            ->findOrFail($this->customerId);

        // Ensure a wallet exists to inspect
        $wallet = CustomerWallet::firstOrCreate(
            ['customer_id' => $this->customerId, 'currency' => 'IQD'],
            [
                'balance_minor' => 0,
                'locked_minor'  => 0,
                'version'       => 1,
            ]
        );

        // Transactions query
        $tx = WalletTransaction::where('wallet_id', $wallet->id)
            ->orderBy('created_at', 'DESC');

        if ($this->tabFilter === 'credit') {
            $tx->where('direction', 'credit');
        } elseif ($this->tabFilter === 'debit') {
            $tx->where('direction', 'debit');
        }

        if (!empty($this->searchReason)) {
            $search = '%' . $this->searchReason . '%';
            $tx->where(function ($q) use ($search) {
                $q->where('reason', 'like', $search)
                  ->orWhere('meta->note', 'like', $search);
            });
        }

        if (!empty($this->startDate)) {
            $tx->whereDate('created_at', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $tx->whereDate('created_at', '<=', $this->endDate);
        }

        if ($this->minAmount !== null && $this->minAmount !== '') {
            $tx->where('amount_minor', '>=', (int) $this->minAmount);
        }

        if ($this->maxAmount !== null && $this->maxAmount !== '') {
            $tx->where('amount_minor', '<=', (int) $this->maxAmount);
        }

        $transactions = $tx->paginate(25);

        // Simple stats
        $creditTotal = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('direction', 'credit')
            ->sum('amount_minor');

        $debitTotal = WalletTransaction::where('wallet_id', $wallet->id)
            ->where('direction', 'debit')
            ->sum('amount_minor');

        return view('super-admins.pages.walletviewer.wallet-viewer', [
            'customer'       => $customer,
            'wallet'         => $wallet,
            'transactions'   => $transactions,
            'creditTotal'    => $creditTotal,
            'debitTotal'     => $debitTotal,
        ]);
    }
}
