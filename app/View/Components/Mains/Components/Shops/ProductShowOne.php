<?php

namespace App\View\Components\Mains\Components\Shops;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductShowOne extends Component
{
    public $products;
    /**
     * Create a new component instance.
     */
    public function __construct($products)
    {
        $this->products = $products;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.shops.product-show-one',[
            "products" => $this->products
        ]);
    }
}
