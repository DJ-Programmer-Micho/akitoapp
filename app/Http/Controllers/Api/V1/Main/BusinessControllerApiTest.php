<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Models\Product;
use App\Models\Category;
use App\Models\WebSetting;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;
use Illuminate\Database\Eloquent\Builder;

class BusinessControllerApi extends Controller
{
    private const CACHE_DURATION = 1; // minutes

    public function homeApi(Request $request)
    {
        $locale = $request->input('lang');
        $settings = $this->getWebSettings();
        
        if (!$settings) {
            return response()->json(['error' => 'Web settings not found'], 404);
        }

        $bannerImages = json_decode($settings->banner_images, true);
        $heroImages = json_decode($settings->hero_images, true);

        return response()->json([
            'locale' => $locale,
            'categoryProducts' => $this->getCategoryProducts($bannerImages, $locale),
            'categoriesData' => $this->getCategoriesData($locale),
            'imageBanner' => $bannerImages,
            'featured_products' => $this->getFeaturedProducts('featured', 'Featured'),
            'on_sale_products' => $this->getFeaturedProducts('on_sale', 'On Sale'),
            'sliders' => $this->getSortedSliders($heroImages, $locale),
        ]);
    }

    private function getWebSettings(): ?WebSetting
    {
        return Cache::remember('web_settings', self::CACHE_DURATION, function () {
            return WebSetting::find(1);
        });
    }

    private function getCategoryProducts(array $bannerImages, string $locale): array
    {
        return array_map(
            fn($banner) => $this->fetchProductsByCategory(
                $banner['category_id'],
                $banner['sub_category_id'] ?? null,
                "",
                $locale
            ),
            $bannerImages
        );
    }

    private function getCategoriesData(string $locale): array
    {
        return Cache::remember(
            "categories_data_$locale",
            self::CACHE_DURATION,
            fn() => Category::where('status', 1)
                ->with(['categoryTranslation' => fn($query) => $query->where('locale', $locale)])
                ->orderBy('priority', 'ASC')
                ->get()
                ->toArray()
        );
    }

    private function getSortedSliders(array $heroImages, string $locale): array
    {
        return collect($heroImages[$locale] ?? [])
            ->sortBy(fn($item) => (int)$item['priority'])
            ->values()
            ->all();
    }

    private function fetchProductsByCategory(int $categoryId, ?int $subCategoryId, string $defaultTitle, string $locale): array
    {
        $customerId = auth('customer')->id();
        $cacheKey = "active_products_category_{$categoryId}_{$subCategoryId}_{$locale}_{$customerId}";
    
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($categoryId, $subCategoryId, $locale, $defaultTitle, $customerId) {
            $products = $this->getProductsQuery($categoryId, $subCategoryId, $locale)
                ->get()
                ->map(fn($product) => $this->formatProduct($product, $customerId));
    
            $firstProduct = Product::find($products->first()['id']); // Retrieve Eloquent model
    
            return [
                'products' => $products,
                'title' => optional($firstProduct?->categories->first()?->categoryTranslation)->title ?? $defaultTitle
            ];
        });
    }
    

    private function getFeaturedProducts(string $type, string $title): array
    {
        $locale = app()->getLocale();
        $customerId = auth('customer')->id();
        $cacheKey = "featured_products_{$type}_{$locale}_{$customerId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($type, $title, $locale, $customerId) {
            $products = Product::with($this->productRelationships($locale))
                ->where('status', 1)
                ->whereHas('variation', fn($query) => $query->where($type, 1))
                ->get()
                ->map(fn($product) => $this->formatProduct($product, $customerId));

            return [
                'products' => $products,
                'title' => $title,
            ];
        });
    }

    private function getProductsQuery(int $categoryId, ?int $subCategoryId, string $locale): Builder
    {
        return Product::with($this->productRelationships($locale))
            ->where('status', 1)
            ->whereHas('categories', fn($query) => $query->where('categories.id', $categoryId))
            ->when($subCategoryId, fn($query) => 
                $query->whereHas('subCategories', fn($subQuery) => 
                    $subQuery->where('sub_categories.id', $subCategoryId)
                )
            )
            ->orderBy('priority', 'ASC');
    }

    private function productRelationships(string $locale): array
    {
        return [
            'productTranslation' => fn($query) => $query->where('locale', $locale),
            'variation' => fn($query) => $query->with([
                'colors', 'sizes', 'materials', 'capacities',
                'images' => fn($query) => $query->where(fn($q) => 
                    $q->where('priority', 0)->orWhere('is_primary', 1)
                )
            ]),
            'brand.brandTranslation' => fn($query) => $query->where('locale', $locale),
            'categories.categoryTranslation' => fn($query) => $query->where('locale', $locale),
            'subCategories.subCategoryTranslation' => fn($query) => $query->where('locale', $locale),
            'tags.tagTranslation' => fn($query) => $query->where('locale', $locale),
        ];
    }

    private function formatProduct($product, ?int $customerId): array
    {
        $finalPrices = $this->calculateFinalPrice($product, $customerId);
    
        return array_merge(
            [
                'id' => $product->id,
                'name' => optional($product->productTranslation->first())->name ?? '',
                'brand' => optional($product->brand->brandTranslation->first())->name ?? '',
                'categories' => $product->categories->map(fn($c) => [
                    'id' => $c->id,
                    'name' => optional($c->categoryTranslation->first())->name ?? ''
                ]),
                'subCategories' => $product->subCategories->map(fn($sc) => [
                    'id' => $sc->id,
                    'name' => optional($sc->subCategoryTranslation->first())->name ?? ''
                ]),
                'price' => $finalPrices['base_price'],
                'discount_price' => $finalPrices['discount_price'],
                'customer_discount_price' => $finalPrices['customer_discount_price'],
                'total_discount_percentage' => $finalPrices['total_discount_percentage']
            ],
            $product->toArray()
        );
    }
    
    
    private function calculateFinalPrice($product, ?int $customerId): array
    {
        $basePrice = $product->variation->price;
        $discountPrice = $product->variation->discount ?? $basePrice;
        $totalDiscountPercentage = 0;

        if ($customerId) {
            $totalDiscountPercentage = $this->calculateCustomerDiscount($product, $customerId);
        }

        $customerDiscountPrice = $discountPrice * (1 - (min($totalDiscountPercentage, 100) / 100));

        return [
            'base_price' => $basePrice,
            'discount_price' => $discountPrice,
            'customer_discount_price' => $customerDiscountPrice,
            'total_discount_percentage' => $totalDiscountPercentage
        ];
    }

    private function calculateCustomerDiscount($product, int $customerId): float
    {
        $discountRules = $this->getDiscountRules($product, $customerId);
        $totalDiscount = 0;

        foreach ($discountRules as $rule) {
            $totalDiscount += match ($rule->type) {
                'product' => $rule->product_id === $product->id ? $rule->discount_percentage : 0,
                'brand' => $rule->brand_id === $product->brand_id ? $rule->discount_percentage : 0,
                'category' => $rule->category_id === $product->categories->first()->id ? $rule->discount_percentage : 0,
                'subcategory' => $rule->sub_category_id === $product->subCategories->first()->id ? $rule->discount_percentage : 0,
                default => 0
            };
        }

        return (float) $totalDiscount;
    }

    private function getDiscountRules($product, int $customerId)
    {
        $cacheKey = "discount_rules_{$customerId}_{$product->id}";
        
        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($product, $customerId) {
            return DiscountRule::where('customer_id', $customerId)
                ->where(fn($query) => $query
                    ->where('product_id', $product->id)
                    ->orWhere('category_id', $product->categories->first()->id)
                    ->orWhere('sub_category_id', $product->subCategories->first()->id)
                    ->orWhere('brand_id', $product->brand_id))
                ->get();
        });
    }
}