<?php

namespace App\View\Components\Mains\Components;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class FeaturedOne extends Component
{
    public $featured;
    public $on_sale;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->featured = Product::with([
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
          ->get();

        //   dd( $this->featured);
     

        

        $this->on_sale = Product::with([
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
                $query->where('on_sale', 1);
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
            ->get();
    
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {

        // dd($this->featured);
        return view('mains.components.featured-one',[
            'featured_products' => $this->featured,
            'on_sale_products' => $this->on_sale
        ]);
    }
}