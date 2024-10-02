<?php

namespace App\Http\Livewire\Account;

use App\Models\Order;
use App\Models\Product;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Auth;

class OrdersListLivewire extends Component
{
    public $orderTable = [];

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
            }, 'variation', 'variation.images']);
        }])
        ->get();

        // foreach ($this->orderTable as $order) {
        //     // Calculate total amount for each order
        //     $order->total_amount = $order->calculateTotalAmount();
        // }
    }


    public function render()
    {
        return view('mains.components.livewire.account.order-list-one', [
            'orderTable' => $this->orderTable,
        ]);
    }
}
