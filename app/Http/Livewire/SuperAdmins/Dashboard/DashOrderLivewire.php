<?php

namespace App\Http\Livewire\SuperAdmins\Dashboard;

use App\Models\Order;
use Livewire\Component;

class DashOrderLivewire extends Component
{
    public $latestOrders;
    protected $listeners = [
        'echo:AdminChannel,EventOrderStatusUpdated' => 'reloadTable',
        'echo:AdminChannel,EventCustomerOrderCheckout' => 'reloadTable',
        'echo:AdminChannel,EventOrderPaymentStatusUpdated' => 'reloadTable',
    ];

    public function reloadTable(){ 
        $this->render();
    }

    public function render(){
        $locale = app()->getLocale();
        $latestOrders = Order::with([
            'orderItems.product' => function ($query) use ($locale) {
                $query->with([
                    'productTranslation' => function ($subQuery) use ($locale) {
                        $subQuery->where('locale', $locale);
                    },
                    'variation',
                    'variation.images'
                ]);
            }
        ])
        ->orderBy('created_at', 'desc')
        ->take(5) // get the latest 5
        ->get();


        return view('super-admins.pages.dashboards.dash_order_history', [
            'orderTable' => $latestOrders,
        ]);
    }
}