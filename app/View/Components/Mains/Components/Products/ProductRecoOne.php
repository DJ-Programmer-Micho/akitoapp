<?php

namespace App\View\Components\Mains\Components\Products;

use Closure;
use App\Models\Product;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;

class ProductRecoOne extends Component
{
    public $recommends;
    public $locale;

    public function __construct($locale)
    {
        $this->recommends = Product::with([
            'productTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'variation.colors',
            'variation.sizes.variationSizeTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'variation.materials',
            'variation.capacities',
            'variation.images',
            'brand.brandTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'categories.categoryTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'tags.tagTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'information.informationTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }
        ])
        ->where('status', 1)
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

          // Process each recommended product
          foreach ($this->recommends as $product) {
              // Fetch the first translation if available
              $product->productTranslation = $product->productTranslation->first();
              $product->information->informationTranslation = $product->information->informationTranslation->first();
          }
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
