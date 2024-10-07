<?php

namespace App\View\Components\Mains\Components\Products;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\ProductRecommendation;

class ProductRecoOne extends Component
{
    public $recommends;
    public $locale;

    public function __construct($locale, $id)
    {
        $this->locale = $locale;

        // Fetch recommended product IDs based on the provided product ID
        $recommendedProductIds = ProductRecommendation::where('product_id', $id)
            ->pluck('recommended_product_id')
            ->toArray(); // Convert to array for empty check

            if(!empty($recommendedProductIds)) {
                $this->recommends = $this->getRecommendedProducts($recommendedProductIds);
            } else {
                $this->recommends = $this->getRecommendedProductsNotDefined();
            }
    }

    private function getRecommendedProducts(array $recommendedProductIds)
    {
        return Product::with([
            'productTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'variation.colors',
            'variation.sizes.variationSizeTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
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
            },
            'information.informationTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            }
        ])
        ->where('status', 1)
        ->whereIn('id', $recommendedProductIds)
        ->get();
    }

    private function getRecommendedProductsNotDefined()
    {
        return Product::with([
            'productTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'variation.colors',
            'variation.sizes.variationSizeTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
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
            },
            'information.informationTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            }
        ])
        ->where('status', 1)
        ->limit(8)
        ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.products.product-reco-one', [
            'recommends' => $this->recommends,
        ]);
    }
}
