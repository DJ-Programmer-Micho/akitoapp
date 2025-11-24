<?php

namespace App\Http\Livewire\Account;

use App\Models\CustomerWallet;
use App\Models\WalletTransaction;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class WalletLivewire extends Component
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
        $customer = Auth::guard('customer')->user();

        if (!$customer) {
            // Not logged in → empty wallet + empty paginator
            $emptyPaginator = new LengthAwarePaginator([], 0, $this->perPage);
            return view('mains.components.livewire.account.wallet-list-one', [
                'wallet'       => null,
                'transactions' => $emptyPaginator,
            ]);
        }

        // Assuming: Customer has relation ->wallet() (hasOne CustomerWallet)
        $wallet = $customer->wallet()->first();

        if (!$wallet) {
            // Customer has no wallet yet → show zeros and empty list
            $emptyPaginator = new LengthAwarePaginator([], 0, $this->perPage);
            return view('mains.components.livewire.account.wallet-list-one', [
                'wallet'       => null,
                'transactions' => $emptyPaginator,
            ]);
        }

        $transactions = WalletTransaction::where('wallet_id', $wallet->id)
            ->orderByDesc('created_at')
            ->paginate($this->perPage);

        return view('mains.components.livewire.account.wallet-list-one', [
            'wallet'       => $wallet,
            'transactions' => $transactions,
        ]);
    }
}
