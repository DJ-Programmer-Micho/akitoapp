<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\WebSetting;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class BusinessControllerApi extends Controller
{
    private const CACHE_DURATION = 1; // Cache duration in minutes

    private function getWebSettings(): ?WebSetting
    {
        return Cache::remember('web_settings', self::CACHE_DURATION, fn() => WebSetting::find(1));
    }

    // 📌 Get Categories
    public function categoriesApi(Request $request)
    {
        $locale = $request->input('lang', app()->getLocale());
        return response()->json($this->getCategoriesData($locale));
    }

    private function getCategoriesData(string $locale): array
    {
        return Cache::remember("categories_data_$locale", self::CACHE_DURATION, function () use ($locale) {
            return Category::where('status', 1)
                ->with(['categoryTranslation' => fn($q) => $q->where('locale', $locale)])
                ->orderBy('priority', 'ASC')
                ->get()
                ->map(fn($category) => [
                    'id'    => $category->id,
                    'name'  => optional($category->categoryTranslation)->name ?? '',
                    'image' => app('cloudfront').$category->image ?? 'default.jpg',
                ])
                ->toArray();
        });
    }

    // 📌 Get Brands
    public function brandsApi()
    {
        return response()->json(Cache::remember("brands_data", self::CACHE_DURATION, function () {
            return Brand::where('status', 1)
                ->with(['brandTranslation' => fn($q) => $q->select('brand_id', 'name')])
                ->orderBy('priority', 'ASC')
                ->get()
                ->map(fn($brand) => [
                    'id'    => $brand->id,
                    'name'  => optional($brand->brandTranslation)->name ?? '',
                    'image' => app('cloudfront').$brand->image ?? 'default.jpg',
                ])
                ->toArray();
        }));
    }

    // 📌 Get Hero Carousel Images
    public function heroCarouselApi(Request $request)
    {
        $locale = $request->input('lang', app()->getLocale());
        $settings = $this->getWebSettings();

        if (!$settings) {
            return response()->json(['error' => 'Web settings not found'], 404);
        }

        $heroImages = json_decode($settings->hero_images, true)[$locale] ?? [];

        $sortedImages = collect($heroImages)
            ->filter(fn($item) => isset($item['priority'])) // Ensure 'priority' exists
            ->sortBy(fn($item) => (int) $item['priority'])
            ->values()
            ->all();

        return response()->json($sortedImages);
    }

    // 📌 Get Featured Products
    public function featuredProductsApi(Request $request)
    {
        return response()->json($this->getProducts('featured', 'Featured', $request->input('lang', app()->getLocale())));
    }

    // 📌 Get On-Sale Products
    public function onSaleProductsApi(Request $request)
    {
        return response()->json($this->getProducts('on_sale', 'On Sale', $request->input('lang', app()->getLocale())));
    }

    public function productsByCategoryApi(Request $request)
    {
        $locale = $request->input('lang', app()->getLocale());
        $settings = $this->getWebSettings();
    
        if (!$settings) {
            return response()->json(['error' => 'Web settings not found'], 404);
        }
    
        // Decode banner images
        $bannerImages = json_decode($settings->banner_images, true);
    
        // Ensure it's a valid array
        if (!is_array($bannerImages)) {
            return response()->json(['error' => 'Invalid banner images format'], 400);
        }
    
        // Loop through each banner entry and fetch category data
        $categoriesWithProducts = collect($bannerImages)->map(function ($banner) use ($locale) {
            return [
                'banner_image' => $banner['image'] ?? 'default_banner.jpg',
                'category_data' => $this->fetchProductsByCategory(
                    (int) $banner['category_id'], 
                    $banner['sub_category_id'] ?? null, 
                    '',
                    $locale
                )
            ];
        });
    
        return response()->json($categoriesWithProducts->values()->all());
    }
    


    // ✅ Helper Functions ✅

    private function getProducts(string $type, string $title, string $locale): array
    {
        return Cache::remember("products_{$type}_{$locale}", self::CACHE_DURATION, function () use ($type, $title, $locale) {
            return [
                'title'    => $title,
                'products' => Product::with($this->productRelationships($locale))
                    ->where('status', 1)
                    ->whereHas('variation', fn($query) => $query->where($type, 1))
                    ->get()
                    ->map(fn($product) => $this->formatProduct($product))
                    ->toArray(),
            ];
        });
    }

    private function fetchProductsByCategory(int $categoryId, ?int $subCategoryId, string $defaultTitle, string $locale): array
    {
        return Cache::remember("active_products_category_{$categoryId}_{$locale}", self::CACHE_DURATION, function () use ($categoryId, $subCategoryId, $locale, $defaultTitle) {
            $products = Product::with($this->productRelationships($locale))
                ->where('status', 1)
                ->whereHas('categories', fn($query) => $query->where('categories.id', $categoryId))
                ->when($subCategoryId, fn($query) => $query->whereHas('subCategories', fn($subQuery) => $subQuery->where('sub_categories.id', $subCategoryId)))
                ->get()
                ->map(fn($product) => $this->formatProduct($product))
                ->toArray();
// return [$products[0]['category']];
            return [
                'title'    => $products[0]['category'] ?? 'Unknown',
                'products' => $products,
            ];
        });
    }

    private function productRelationships(string $locale): array
    {
        return [
            'productTranslation'       => fn($q) => $q->where('locale', $locale),
            'variation.images'         => fn($q) => $q->where('is_primary', 1),
            'brand.brandTranslation'   => fn($q) => $q->where('locale', $locale),
            'categories.categoryTranslation' => fn($q) => $q->where('locale', $locale),
        ];
    }

    private function formatProduct(Product $product): array
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $finalPrices = $this->calculateFinalPrice($product, $customerId);
// return ['asd',$product];
        return [
            'id'                     => $product->id,
            'name'                   => $product->productTranslation->first()->name ?? '',
            'slug'                   => $product->productTranslation->first()->slug ?? '',
            'brand'                  => $product->brand->brandTranslation->name ?? '',
            'category'               => $product->categories->first()->categoryTranslation->name ?? '',
            'image'                  => app('cloudfront').$product->variation->images->first()->image_path ?? 'https://d1h4q8vrlfl3k9.cloudfront.net/web-setting/logo/icon_2024021117305762286939.png',
            'price'                  => $finalPrices['base_price'],
            'discount_price'         => $finalPrices['customer_discount_price'] ?? $finalPrices['discount_price'],
        ];
    }

    private function calculateFinalPrice(Product $product, ?int $customerId): array
{
    $basePrice = $product->variation->price;
    $discountPrice = $product->variation->discount ?? null; // If no discount, keep it null
    $customerDiscountPrice = $discountPrice ?? $basePrice; // If no discount, use base price
    $totalDiscountPercentage = 0;

    if ($customerId) {
        $discountRules = DiscountRule::where('customer_id', $customerId)
            ->where(fn($query) => $query->where('product_id', $product->id)
                ->orWhere('category_id', optional($product->categories->first())->id)
                ->orWhere('sub_category_id', optional($product->subCategories->first())->id)
                ->orWhere('brand_id', $product->brand_id))
            ->get();

        foreach ($discountRules as $rule) {
            $totalDiscountPercentage += $rule->discount_percentage;
        }

        $totalDiscountPercentage = min($totalDiscountPercentage, 100);

        // Apply customer discount if applicable
        if ($totalDiscountPercentage > 0) {
            $customerDiscountPrice = $customerDiscountPrice * (1 - ($totalDiscountPercentage / 100));
        }
    }

    return [
        'base_price'              => number_format($basePrice, 2, '.', ''),
        'discount_price'          => $discountPrice !== null ? number_format($discountPrice, 2, '.', '') : null,
        'customer_discount_price' => $customerDiscountPrice != $basePrice ? number_format($customerDiscountPrice, 2, '.', '') : null,
    ];
}

}
