<?php

namespace App\View\Components\Mains\Mappings\Components;

use Closure;
use App\Models\CartItem;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;

class LogoOne extends Component
{
    public $cartItems;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->cartItems = CartItem::where('customer_id', Auth::guard('customer')->id())->with('product')->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.mappings.components.logo-one',[
            'cartItems' =>$this->cartItems
        ]);
    }
}
