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

        // Fetch featured products with translations for the current locale
        $this->featured = Product::with([
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
        ->whereHas('variation', function ($query) {
            $query->where('featured', 1);
        })
        ->whereHas('brand', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('categories', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('tags', function ($query) {
            $query->where('status', 1);
        })
        ->get();

        // Fetch on-sale products with translations for the current locale
        $this->on_sale = Product::with([
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
        ->whereHas('variation', function ($query) {
            $query->where('on_sale', 1);
        })
        ->whereHas('brand', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('categories', function ($query) {
            $query->where('status', 1);
        })
        ->whereHas('tags', function ($query) {
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
