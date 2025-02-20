<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Models\VariationSize;
use App\Models\VariationColor;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ProductDetailControllerApi extends Controller
{
    public function productDetailApi(Request $request, $id)
    {
        // Fetch product with necessary relationships
        $locale = $request->input('lang', app()->getLocale());
        $product = Product::with([
            'productTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.colors',
            'variation.sizes.variationSizeTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.materials.variationMaterialTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.capacities.variationCapacityTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.intensity',
            'variation.images' => fn($q) => $q->orderBy('priority'), // Order images by priority
            'brand.brandTranslation' => fn($q) => $q->where('locale', $locale),
            'categories.categoryTranslation' => fn($q) => $q->where('locale', $locale),
            'subCategories.subCategoryTranslation' => fn($q) => $q->where('locale', $locale),
            'tags.tagTranslation' => fn($q) => $q->where('locale', $locale),
            'information.informationTranslation' => fn($q) => $q->where('locale', $locale),
        ])
        ->where('id', $id) // Fetch by ID instead of slug
        ->first();

        // If product not found, return 404 response
        if (!$product) {
            return response()->json(['message' => 'Product not found'], 404);
        }

        // Extract translations
        $product->productTranslation = $product->productTranslation->first();
        $product->information->informationTranslation = $product->information->informationTranslation->first() ?? null;

        // Calculate final price
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $discountDetails = $this->calculateFinalPrice($product, $customerId);

        // Format product data
        $formattedProduct = $this->formatProductDetail($product, $discountDetails);

        return response()->json([
            'product' => $formattedProduct,
        ]);
    }

    private function formatProductDetail(Product $product, array $discountDetails): array
    {
        return [
            'id'              => $product->id,
            'name'            => $product->productTranslation->name ?? '',
            'slug'            => $product->productTranslation->slug ?? '',
            'description'     => $product->productTranslation->description ?? '',
            'brand'           => optional($product->brand->brandTranslation)->name ?? '',
            'category'        => optional($product->categories->first()->categoryTranslation)->name ?? '',
            'sub_category'    => optional($product->subCategories->first()->subCategoryTranslation)->name ?? '',
            'tags'            => $product->tags->pluck('tagTranslation.name')->filter()->values(),
            'images'          => $product->variation->images->map(fn($img) => app('cloudfront').$img->image_path),
            'price'           => $discountDetails['base_price'],
            'discount_price'  => $discountDetails['customer_discount_price'] ?? $discountDetails['discount_price'],
            'is_discounted'   => !empty($discountDetails['discount_price']),
            'variations'      => [
                'colors'    => $product->variation->colors->pluck('name'),
                'sizes'     => $product->variation->sizes->map(fn($size) => [
                    'id'   => $size->id,
                    'name' => optional($size->variationSizeTranslation)->name ?? '',
                ]),
                'capacities' => $product->variation->capacities->map(fn($cap) => [
                    'id'   => $cap->id,
                    'name' => optional($cap->variationCapacityTranslation)->name ?? '',
                ]),
                'materials' => $product->variation->materials->map(fn($mat) => [
                    'id'   => $mat->id,
                    'name' => optional($mat->variationMaterialTranslation)->name ?? '',
                ]),
                'intensity' => [
                    'intensityMin' => $product->variation->intensity[0]['min'] ?? '',
                    'intensityMax' => $product->variation->intensity[0]['max'] ?? '',
                ],

            ],
            'seo' => [
                'title'       => $product->productTranslation->name ?? 'Akitu Product',
                'keywords'    => Str::limit($product->variation->keywords, 160) ?? 'akitu, coffee shop',
                'image'       => app('cloudfront').$product->variation->images->first()->image_path ?? 'default_image_url',
            ],
            'information' => [
                'description'     => $product->information->informationTranslation->description ?? '',
                'aditionalInformation' => $product->information->informationTranslation->addition ?? '',
                'shippingReturning' => $product->information->informationTranslation->shipping ?? '',
                'faq' => json_decode($product->information->informationTranslation->question_and_answer, true) ?? [],
            ],
            'created_at'      => $product->created_at->format('Y-m-d H:i:s'),
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