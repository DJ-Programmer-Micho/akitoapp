<?php

namespace App\View\Components\Mains\Components\Products;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProductRecoOne extends Component
{
    public $product;
    public $recommends;

    public function __construct()
    {
        $this->recommends = Product::with([
            'productTranslation', 
            'variation.colors', 
            'variation.sizes', 
            'variation.materials', 
            'variation.capacities',
            'variation.images',
            'brand.brandTranslation', 
            'categories.categoryTranslation', 
            'tags.tagTranslation'
        ])->where('status', 1)
          ->whereHas('variation', function($query) {
              $query->where('featured', 1);
          })
          ->whereHas('brand', function($query) {
              $query->where('status', 1);
          })
          ->whereHas('categories', function($query) {
              $query->where('status', 1);
          })
          ->whereHas('tags', function($query) {
              $query->where('status', 1);
          })
          ->limit(5)
          ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.products.product-reco-one',[
            'recommends' => $this->recommends,
        ]);
    }
}
