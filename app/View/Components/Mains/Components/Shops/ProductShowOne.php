<?php

namespace App\View\Components\Mains\Components\Shops;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductShowOne extends Component
{
    public $products;
    public $grid;
    /**
     * Create a new component instance.
     */
    public function __construct($products, $grid)
    {
        $this->products = $products;
        $this->grid = $grid;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        if($this->grid == 1){
            return view('mains.components.shops.product-show-one',[
                "products" => $this->products
            ]);
        } else if ($this->grid == 3) {
            return view('mains.components.shops.product-show-three',[
                "products" => $this->products
            ]);
        } elseif($this->grid == 2) {
            return view('mains.components.shops.product-show-two',[
                "products" => $this->products
            ]);
        } else {
            return view('mains.components.shops.product-show-four',[
                "products" => $this->products
            ]);
        }

    }
}
