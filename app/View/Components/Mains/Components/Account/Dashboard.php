<?php

namespace App\View\Components\Mains\Components\Account;

use Closure;
use App\Models\Order;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public $totalPendingAndShipping;
    public $totalShipping;
    public $totalPending;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $cust_id = auth('customer')->id();
        
        // Count orders where the status is either 'pending' or 'shipping'
        $this->totalPendingAndShipping = Order::where('customer_id', $cust_id)
            ->whereIn('status', ['pending', 'shipping'])
            ->count();

        // Count orders where the status is 'shipping'
        $this->totalShipping = Order::where('customer_id', $cust_id)
            ->where('status', 'shipping')
            ->count();

        // Count orders where the status is 'pending'
        $this->totalPending = Order::where('customer_id', $cust_id)
            ->where('status', 'pending')
            ->count();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.account.dashboard',[
            'totalPendingAndShipping' => $this->totalPendingAndShipping,
            'totalShipping' => $this->totalShipping,
            'totalPending' => $this->totalPending
        ]);
    }
}
