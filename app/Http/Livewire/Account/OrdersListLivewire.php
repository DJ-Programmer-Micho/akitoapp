<?php

namespace App\Http\Livewire\Account;

use App\Models\Order;
use App\Models\CustomerWallet;
use App\Models\WalletTransaction;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OrdersListLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';
    public $perPage = 10;

    /**
     * Reset page when Livewire pagination changes via query string.
     */
    public function updatingPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        $locale   = app()->getLocale();
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            return view('mains.components.livewire.account.order-list-one', [
                'orderTable'       => collect(),
                'refundPendingMap' => [],
                'refundRejectedMap'=> [],
            ]);
        }

        // Orders
        $orders = Order::where('customer_id', $customer->id)
            ->with([
                'orderItems.product' => function ($query) use ($locale) {
                    $query->with([
                        'productTranslation' => function ($subQuery) use ($locale) {
                            $subQuery->where('locale', $locale);
                        },
                        'variation',
                        'variation.images' => function ($imageQuery) {
                            $imageQuery->where(function ($imageQuery) {
                                $imageQuery->where('priority', 0)
                                        ->orWhere('is_primary', 1);
                            });
                        },
                    ]);
                },
            ])
            ->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        // Wallet + refund state
        $refundPendingMap  = []; // order_id => bool (net locked > 0)
        $refundRejectedMap = []; // order_id => bool (admin rejected at least once)

        $wallet = CustomerWallet::where('customer_id', $customer->id)
            ->where('currency', 'IQD')
            ->first();

        if ($wallet) {
            // We care about locks and their voids
            $txs = WalletTransaction::where('wallet_id', $wallet->id)
                ->whereIn('reason', ['refund_lock', 'refund_void'])
                ->get();

            // Track net locked amount per order, and whether there was any void
            $netLockPerOrder = [];   // order_id => int
            $hasVoidPerOrder = [];   // order_id => bool

            foreach ($txs as $tx) {
                $meta = is_array($tx->meta)
                    ? $tx->meta
                    : (json_decode($tx->meta ?? '[]', true) ?: []);

                $orderId = $meta['order_id'] ?? null;
                if (!$orderId) {
                    continue;
                }

                if ($tx->reason === 'refund_lock') {
                    $netLockPerOrder[$orderId] = ($netLockPerOrder[$orderId] ?? 0)
                        + (int) $tx->amount_minor;
                } elseif ($tx->reason === 'refund_void') {
                    $netLockPerOrder[$orderId] = ($netLockPerOrder[$orderId] ?? 0)
                        - (int) $tx->amount_minor;
                    $hasVoidPerOrder[$orderId] = true;
                }
            }

            // Build simple boolean maps for the Blade
            foreach ($netLockPerOrder as $orderId => $net) {
                if ($net > 0) {
                    $refundPendingMap[$orderId] = true;
                } elseif ($net <= 0 && !empty($hasVoidPerOrder[$orderId])) {
                    // No net lock but user had a void => rejected
                    $refundRejectedMap[$orderId] = true;
                }
            }
        }

        return view('mains.components.livewire.account.order-list-one', [
            'orderTable'       => $orders,
            'refundPendingMap' => $refundPendingMap,
            'refundRejectedMap'=> $refundRejectedMap,
        ]);
    }

}
