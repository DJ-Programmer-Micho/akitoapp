<?php

namespace App\View\Components\Mains\Components;

use Closure;
use App\Models\Product;
use App\Models\DiscountRule;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class FeaturedOne extends Component
{
    public $featured;
    public $on_sale;
    public $locale;
    public $discountRules = [];
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        // Get the current locale
        $this->locale = app()->getLocale();
        $this->featured = $this->fetchProducts('featured', 1);
        $this->on_sale = $this->fetchProducts('on_sale', 1);

        if (Auth::guard('customer')->check()) {
            $this->discountRules = $this->getDiscountRules(Auth::guard('customer')->user()->id);
        }
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

    // Fetch discount rules for a specific customer
    private function getDiscountRules($customerId)
    {
        return DiscountRule::where('customer_id', $customerId)
            ->where('type', 'brand') // Adjust this query as needed
            ->orWhere('type', 'category')
            ->orWhere('type', 'subcategory')
            ->orWhere('type', 'product')
            ->get();
    }

    private function calculateFinalPrice($product)
    {
        $basePrice = $product->variation->price;
        $finalPrice = $basePrice;

        // Check for applicable discount rules
        foreach ($this->discountRules as $discountRule) {
            if ($discountRule->type === 'brand' && $product->brand_id == $discountRule->brand_id) {
                $finalPrice *= (1 - ($discountRule->discount_percentage / 100));
            } elseif ($discountRule->type === 'category' && $product->categories->contains($discountRule->category_id)) {
                $finalPrice *= (1 - ($discountRule->discount_percentage / 100));
            } elseif ($discountRule->type === 'subcategory' && $product->sub_category_id == $discountRule->sub_category_id) {
                $finalPrice *= (1 - ($discountRule->discount_percentage / 100));
            } elseif ($discountRule->type === 'product' && $product->id == $discountRule->product_id) {
                $finalPrice *= (1 - ($discountRule->discount_percentage / 100));
            }
        }

        return $finalPrice;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.featured-one', [
            'featured_products' => $this->featured,
            'on_sale_products' => $this->on_sale,
            'calculateFinalPrice' => [$this, 'calculateFinalPrice'],
        ]);
    }
}
