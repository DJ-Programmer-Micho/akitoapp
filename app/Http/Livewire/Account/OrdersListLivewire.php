<?php

namespace App\Http\Livewire\Account;

use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\OrderItem;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class OrdersListLivewire extends Component
{
    use WithPagination;
    public $perPage = 10;
    protected $orderTable = [];

    public function mount()
    {
        $this->loadOrderTable();
    }

    public function loadOrderTable()
    {
        $locale = app()->getLocale(); // Get the current locale
        $this->orderTable = Order::where('customer_id', Auth::guard('customer')->id())
        ->with(['orderItems.product' => function ($query) use ($locale) {
            $query->with(['productTranslation' => function ($subQuery) use ($locale) {
                // Fetch the translation for the current locale
                $subQuery->where('locale', $locale);
            }, 'variation', 'variation.images' => function ($imageQuery) {
                // Filter images to include only those with priority 0 or is_primary 1
                $imageQuery->where(function ($imageQuery) {
                    $imageQuery->where('priority', 0)
                               ->orWhere('is_primary', 1);
                });
            }]);
        }])
        ->orderBy('created_at', 'DESC')
        ->paginate($this->perPage);
    }

    public function updatingPage()
    {
        $this->resetPage();
    }

    public function render()
    {
        return view('mains.components.livewire.account.order-list-one', [
            'orderTable' => $this->orderTable,
        ]);
    }
}
