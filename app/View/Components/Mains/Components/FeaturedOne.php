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
    public $locale;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Get the current locale
        $this->locale = app()->getLocale();
        $this->featured = $this->fetchProducts('featured', 1);
        $this->on_sale = $this->fetchProducts('on_sale', 1);
    }

    private function fetchProducts($type, $status)
    {
        return Product::with([
            'productTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'variation.colors',
            'variation.sizes',
            'variation.materials',
            'variation.capacities',
            'variation.images',
            'brand.brandTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'categories.categoryTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'tags.tagTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            }
        ])
        ->where('status', 1)
        ->whereHas('variation', function ($query) use ($type) {
            $query->where($type, 1);
        })
        ->whereHas('brand', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('categories', function ($query) {
            $query->where('status', 1);
        })
        ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.featured-one', [
            'featured_products' => $this->featured,
            'on_sale_products' => $this->on_sale
        ]);
    }
}
