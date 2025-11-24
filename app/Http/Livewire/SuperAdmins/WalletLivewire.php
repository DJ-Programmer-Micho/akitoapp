<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;          // adjust if your customer model is different
use Illuminate\Support\Facades\Log;

class WalletLivewire extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $searchTerm = '';
    public $startDate;
    public $endDate;
    public $statusFilter = ''; // 1=Active, 0=Block, '' = all

    protected $queryString = [
        'searchTerm'   => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'startDate'    => ['except' => null],
        'endDate'      => ['except' => null],
        'page'         => ['except' => 1],
    ];

    // Reset page when filters change
    public function updatingSearchTerm()   { $this->resetPage(); }
    public function updatingStatusFilter() { $this->resetPage(); }
    public function updatingStartDate()    { $this->resetPage(); }
    public function updatingEndDate()      { $this->resetPage(); }

    public function render()
    {
        $customerImg = app('userImg'); // same style as your other lists

        $query = Customer::with(['customer_profile', 'wallet'])
            ->where('email_verify', '=', 1)
            ->where('phone_verify', '=', 1)
            ->where('company_verify', '=', 1)
            ->orderBy('created_at', 'DESC');

        if ($this->statusFilter !== '') {
            $query->where('status', (int) $this->statusFilter);
        }

        if (!empty($this->searchTerm)) {
            $search = '%' . $this->searchTerm . '%';
            $query->where(function ($q) use ($search) {
                $q->where('username', 'like', $search)
                    ->orWhere('email', 'like', $search)
                    ->orWhereHas('customer_profile', function ($sub) use ($search) {
                        $sub->where('first_name', 'like', $search)
                            ->orWhere('last_name', 'like', $search)
                            ->orWhere('phone_number', 'like', $search)
                            ->orWhere('country', 'like', $search)
                            ->orWhere('city', 'like', $search);
                    });
            });
        }

        if (!empty($this->startDate)) {
            $query->whereDate('created_at', '>=', $this->startDate);
        }

        if (!empty($this->endDate)) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

        $tableData = $query->paginate(15);

        return view('super-admins.pages.wallet.wallet-table', [
            'tableData'   => $tableData,
            'customerImg' => $customerImg,
        ]);
    }
}
