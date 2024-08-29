<?php

namespace App\View\Components\Mains\Components\Products;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ProductViewOne extends Component
{
    public $productDetail;
    /**
     * Create a new component instance.
     */
    public function __construct($product)
    {
        $this->productDetail = $product;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.products.product-view-one',[
            "productDetail" => $this->productDetail
        ]);
    }
}
