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
    private const CACHE_DURATION = 5; // Cache duration in minutes

    public function homeApi(Request $request)
    {
        $locale = $request->input('lang', app()->getLocale()); // Default to app locale if not provided
        $settings = $this->getWebSettings();

        if (!$settings) {
            return response()->json(['error' => 'Web settings not found'], 404);
        }

        return response()->json([
            'locale'            => $locale,
            'categories'        => $this->getCategoriesData($locale),
            'featured_products' => $this->getProducts('featured', 'Featured', $locale),
            'on_sale_products'  => $this->getProducts('on_sale', 'On Sale', $locale),
            'sliders'           => $this->getSliders($settings, $locale),
        ]);
    }

    private function getWebSettings(): ?WebSetting
    {
        return Cache::remember('web_settings', self::CACHE_DURATION, fn() => WebSetting::find(1));
    }

    private function getCategoriesData(string $locale): array
    {
        return Cache::remember("categories_data_$locale", self::CACHE_DURATION, function () use ($locale) {
            return Category::where('status', 1)
                ->with(['categoryTranslation' => fn($q) => $q->where('locale', $locale)])
                ->orderBy('priority', 'ASC')
                ->get(['id'])
                ->map(fn($category) => [
                    'id'   => $category->id,
                    'name' => optional($category->categoryTranslation)->name ?? '',
                ])
                ->toArray();
        });
    }

    private function getSliders(WebSetting $settings, string $locale): array
    {
        $heroImages = json_decode($settings->hero_images, true);
        return collect($heroImages[$locale] ?? [])
            ->sortBy(fn($item) => (int) $item['priority'])
            ->values()
            ->all();
    }

    private function getProducts(string $type, string $title, string $locale): array
    {
        $customerId = auth('customer')->id() ?? null;
        $cacheKey = "products_{$type}_{$locale}_{$customerId}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($type, $title, $locale, $customerId) {
            $products = Product::with($this->productRelationships($locale))
                ->where('status', 1)
                ->whereHas('variation', fn($query) => $query->where($type, 1))
                ->get()
                ->map(fn($product) => $this->formatProduct($product, $customerId));

            return [
                'title'    => $title,
                'products' => $products,
            ];
        });
    }

    private function productRelationships(string $locale): array
    {
        return [
            'productTranslation'      => fn($q) => $q->where('locale', $locale),
            'variation'               => fn($q) => $q->with(['images' => fn($q) => $q->where('is_primary', 1)]),
            'brand.brandTranslation'  => fn($q) => $q->where('locale', $locale),
            'categories.categoryTranslation' => fn($q) => $q->where('locale', $locale),
        ];
    }

    private function formatProduct(Product $product, ?int $customerId): array
    {
        $finalPrices = $this->calculateFinalPrice($product, $customerId);

        return [
            'id'                     => $product->id,
            'name'                   => optional($product->productTranslation)->name ?? '',
            'slug'                   => optional($product->productTranslation)->slug ?? '',
            'brand'                  => optional($product->brand->brandTranslation)->name ?? '',
            'category'               => optional($product->categories->first()->categoryTranslation)->name ?? '',
            'image'                  => optional($product->variation->images->first())->image_path ?? 'default.jpg',
            'price'                  => $finalPrices['base_price'],
            'discount_price'         => $finalPrices['discount_price'],
            'customer_discount_price' => $finalPrices['customer_discount_price'],
        ];
    }

    private function calculateFinalPrice(Product $product, ?int $customerId): array
    {
        $basePrice = $product->variation->price;
        $discountPrice = $product->variation->discount ?? $basePrice;
        $totalDiscountPercentage = 0;

        if ($customerId) {
            $totalDiscountPercentage = $this->calculateCustomerDiscount($product, $customerId);
        }

        $customerDiscountPrice = $discountPrice * (1 - min($totalDiscountPercentage, 100) / 100);

        return [
            'base_price'               => $basePrice,
            'discount_price'           => $discountPrice,
            'customer_discount_price'  => $customerDiscountPrice,
        ];
    }

    private function calculateCustomerDiscount(Product $product, int $customerId): float
    {
        $discountRules = $this->getDiscountRules($product, $customerId);
        $totalDiscount = 0;

        foreach ($discountRules as $rule) {
            $totalDiscount += match ($rule->type) {
                'product' => $rule->product_id === $product->id ? $rule->discount_percentage : 0,
                'brand' => $rule->brand_id === $product->brand_id ? $rule->discount_percentage : 0,
                'category' => $rule->category_id === optional($product->categories->first())->id ? $rule->discount_percentage : 0,
                default => 0,
            };
        }

        return (float) $totalDiscount;
    }

    private function getDiscountRules(Product $product, int $customerId)
    {
        $cacheKey = "discount_rules_{$customerId}_{$product->id}";

        return Cache::remember($cacheKey, self::CACHE_DURATION, function () use ($product, $customerId) {
            return DiscountRule::where('customer_id', $customerId)
                ->where(fn($q) => $q->where('product_id', $product->id)
                    ->orWhere('category_id', optional($product->categories->first())->id)
                    ->orWhere('brand_id', $product->brand_id)
                )
                ->get();
        });
    }
}
