<?php

namespace App\Http\Controllers\Api\V1\Main;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use App\Models\VariationSize;
use App\Models\VariationColor;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ShopControllerApi extends Controller
{
    private const CACHE_DURATION = 1; // Cache duration in minutes

    /**
     * 📌 Fetch shop products with filters & sorting
     */
    public function productShopApi(Request $request)
    {
        $locale = $request->input('lang', 'en');
        app()->setLocale($locale);
        
        $filters = $this->getFiltersFromRequestApi($request);
        $filters['is_spare_part'] = 0; // Non-spare parts

        // Base product query
        $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
            ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
            ->with($this->getEagerLoadRelationsApi())
            ->where('products.status', 1)
            ->where('products.is_spare_part', 0);

        // Apply filters & sorting dynamically
        $productQuery = $this->applyFiltersApi($productQuery, $filters);
        $productQuery = $this->applySortingApi($productQuery, $filters['sortBy']);

        // Paginate products
        $products = $productQuery->paginate(12);
        $products->getCollection()->transform(fn($product) => $this->applyDiscountsApi($product, $request));
        $formattedProducts = $products->getCollection()->map(fn($product) => $this->formatProduct($product));

        return response()->json([
            'filters'  => $this->getFilterQueriesApi($filters['categoryIds'], 0), // Return available filters
            'products' => $formattedProducts,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    public function productShopAllApi(Request $request)
    {
        $locale = $request->input('lang', 'en');
        app()->setLocale($locale);
        
        $filters = $this->getFiltersFromRequestApi($request);
        $filters['is_spare_part'] = 0; // Non-spare parts

        // Base product query
        $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
            ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
            ->with($this->getEagerLoadRelationsApi())
            ->where('products.is_spare_part', 0);

        // Apply filters & sorting dynamically
        $productQuery = $this->applyFiltersApi($productQuery, $filters);
        $productQuery = $this->applySortingApi($productQuery, $filters['sortBy']);

        // Paginate products
        $products = $productQuery->paginate(12);
        $products->getCollection()->transform(fn($product) => $this->applyDiscountsApi($product, $request));
        $formattedProducts = $products->getCollection()->map(fn($product) => $this->formatProduct($product));

        return response()->json([
            'filters'  => $this->getFilterQueriesApi($filters['categoryIds'], 0), // Return available filters
            'products' => $formattedProducts,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'per_page'     => $products->perPage(),
                'total'        => $products->total(),
                'last_page'    => $products->lastPage(),
            ],
        ]);
    }

    private function formatProduct(Product $product): array
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $finalPrices = $this->calculateFinalPrice($product, $customerId);
    
        return [
            'id'              => $product->id,
            'name'            => $product->productTranslation->first()->name ?? '',
            'slug'            => $product->productTranslation->first()->slug ?? '',
            'brand'           => $product->brand->brandTranslation->name ?? '',
            'category'        => $product->categories->first()->categoryTranslation->name ?? '',
            'image'           => app('cloudfront').($product->variation->images->first()->image_path ?? 'https://d1h4q8vrlfl3k9.cloudfront.net/web-setting/logo/icon_2024021117305762286939.png'),
            'price'           => $finalPrices['base_price'],
            'discount_price'  => $finalPrices['customer_discount_price'] ?? $finalPrices['discount_price'],
    
            // Additional fields:
            'size' => $product->variation->sizes->map(function ($size) {
                return [
                    'id'   => $size->id,
                    'name' => $size->variationSizeTranslation->name ?? '',
                    'code' => $size->code,
                ];
            })->toArray(),
    
            'material' => $product->variation->materials->map(function ($material) {
                return [
                    'id'   => $material->id,
                    'name' => $material->variationMaterialTranslation->name ?? '',
                    'code' => $material->code,
                ];
            })->toArray(),
    
            'capacity' => $product->variation->capacities->map(function ($capacity) {
                return [
                    'id'   => $capacity->id,
                    'name' => $capacity->variationCapacityTranslation->name ?? '',
                    'code' => $capacity->code,
                ];
            })->toArray(),
    
            'color' => $product->variation->colors->map(function ($color) {
                return [
                    'id'   => $color->id,
                    'name' => $color->variationColorTranslation->name ?? '',
                    'code' => $color->code,
                ];
            })->toArray(),
    
            // Assuming the product variation has an intensity value (or a related intensity model)
            'intensity' => $product->variation->intensity ?? null,
    
            // If a product can have multiple tags, return them as an array:
            'tag' => $product->tags->map(function ($tag) {
                return [
                    'id'   => $tag->id,
                    'name' => $tag->tagTranslation->first()->name ?? '',
                    'icon' => $tag->icon ?? '',
                ];
            })->toArray(),
        ];
    }
    


    private function getFilterQueriesApi($categoryIds, $sparepart)
    {
        return [
            'categories' => Category::where('status', 1)
                ->with(['categoryTranslation' => fn($q) => $q->where('locale', app()->getLocale())])
                ->whereHas('product', fn($q) => $q->where('is_spare_part', $sparepart))
                ->orderBy("priority", "asc")
                ->get()
                ->map(fn($category) => [
                    'id'   => $category->id,
                    'name' => optional($category->categoryTranslation)->name ?? '',
                    'slug' => optional($category->categoryTranslation)->slug ?? '',
                    'image' => app('cloudfront').$category->image ?? '',
                ]),
                
                'brands' => Brand::where('status', 1)
                ->whereHas('product', fn($q) => $q->where('is_spare_part', $sparepart))
                ->with(['brandTranslation' => fn($q) => $q->where('locale', app()->getLocale())])
                ->orderBy("priority", "asc")
                ->get()
                ->map(fn($brand) => [
                    'id'   => $brand->id,
                    'name' => optional($brand->brandTranslation)->name ?? '',
                    'slug' => optional($brand->brandTranslation)->slug ?? '',
                    'image' => app('cloudfront').$brand->image ?? '',
                ]),
    
            'subCategories' => SubCategory::where('status', 1)
                ->when($categoryIds, fn($q) => $q->whereHas('product.categories', fn($subQ) => $subQ->whereIn('category_id', $categoryIds)))
                ->whereHas('product', fn($q) => $q->where('is_spare_part', $sparepart))
                ->with(['subCategoryTranslation' => fn($q) => $q->where('locale', app()->getLocale())])
                ->orderBy("priority", "asc")
                ->get()
                ->map(fn($subCategory) => [
                    'id'   => $subCategory->id,
                    'name' => optional($subCategory->subCategoryTranslation)->name ?? '',
                    'slug' => optional($subCategory->subCategoryTranslation)->slug ?? '',
                ]),
                
                'sizes' => VariationSize::where('status', 1)
                ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', $sparepart))
                ->with(['variationSizeTranslation' => fn($q) => $q->where('locale', app()->getLocale())])
                ->orderBy("priority", "asc")
                ->get()
                ->map(fn($size) => [
                    'id'   => $size->id,
                    'name' => optional($size->variationSizeTranslation)->name ?? '',
                    'code' => $size->code,
                ]),
                
                'colors' => VariationColor::where('status', 1)
                ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', $sparepart))
                ->get()
                ->map(fn($color) => [
                    'id'   => $color->id,
                    'name' => optional($color->variationColorTranslation)->name ?? '',
                    'code' => $color->code,
                ]),
    
            'capacities' => VariationCapacity::where('status', 1)
                ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', $sparepart))
                ->orderBy("priority", "asc")
                ->get()
                ->map(fn($capacity) => [
                    'id'   => $capacity->id,
                    'name' => optional($capacity->variationCapacityTranslation)->name ?? '',
                    'code' => $capacity->code, // Assuming `code` contains meaningful data
                ]),
    
            'materials' => VariationMaterial::where('status', 1)
                ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', $sparepart))
                ->orderBy("priority", "asc")
                ->get()
                ->map(fn($material) => [
                    'id'   => $material->id,
                    'name' => optional($material->variationMaterialTranslation)->name ?? '',
                    'code' => $material->code, // Assuming `code` contains meaningful data
                ]),
        ];
    }
    /**
     * 📌 Extract filters from request
     */
    private function getFiltersFromRequestApi(Request $request)
    {
        return [
            'brandIds'      => $request->input('brands', []),
            'categoryIds'   => $request->input('categories', []),
            'subCategoryIds'=> $request->input('subcategories', []),
            'sizeIds'       => $request->input('sizes', []),
            'colorIds'      => $request->input('colors', []),
            'capacityIds'   => $request->input('capacities', []),
            'materialIds'   => $request->input('materials', []),
            'minPrice'      => floatval($request->input('min_price', 0)),
            'maxPrice'      => floatval($request->input('max_price', 5000000)),
            'sortBy'        => $request->input('sortby', 'priority'),
        ];
    }

    /**
     * 📌 Eager-load necessary relationships
     */
    private function getEagerLoadRelationsApi()
    {
        $locale = app()->getLocale();

        return [
            'productTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.images'   => fn($q) => $q->where('is_primary', 1),
            'variation.sizes.variationSizeTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.materials.variationMaterialTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.capacities.variationCapacityTranslation' => fn($q) => $q->where('locale', $locale),
            'variation.colors.variationColorTranslation' => fn($q) => $q->where('locale', $locale),
            'brand.brandTranslation' => fn($q) => $q->where('locale', $locale),
            'categories.categoryTranslation' => fn($q) => $q->where('locale', $locale),
            'tags.tagTranslation' => fn($q) => $q->where('locale', $locale),
        ];
    }

    /**
     * 📌 Apply filters to query
     */
    private function applyFiltersApi($query, $filters)
    {
        return $query
            ->when($filters['categoryIds'], fn($query) => 
                $query->whereHas('categories', fn($q) => $q->whereIn('category_id', $filters['categoryIds']))
            )
            ->when($filters['brandIds'], fn($query) =>
                $query->whereIn('products.brand_id', $filters['brandIds'])
            )
            ->when($filters['subCategoryIds'], fn($query) =>
                $query->whereHas('subCategories', fn($q) => $q->whereIn('sub_category_id', $filters['subCategoryIds']))
            )
            ->when($filters['sizeIds'], fn($query) =>
                $query->whereHas('variation.sizes', fn($q) => $q->whereIn('variation_size_id', $filters['sizeIds']))
            )
            ->when($filters['colorIds'], fn($query) =>
                $query->whereHas('variation.colors', fn($q) => $q->whereIn('variation_color_id', $filters['colorIds']))
            )
            ->when($filters['capacityIds'], fn($query) =>
                $query->whereHas('variation.capacities', fn($q) => $q->whereIn('variation_capacity_id', $filters['capacityIds']))
            )
            ->when($filters['materialIds'], fn($query) =>
                $query->whereHas('variation.materials', fn($q) => $q->whereIn('variation_material_id', $filters['materialIds']))
            )
            ->when(isset($filters['minPrice']) || isset($filters['maxPrice']), function ($query) use ($filters) {
                $minPrice = $filters['minPrice'] ?? Product::min('variation_price'); 
                $maxPrice = $filters['maxPrice'] ?? Product::max('variation_price'); 
    
                if ($minPrice <= $maxPrice) {
                    $query->whereBetween('product_variations.price', [$minPrice, $maxPrice]);
                }
            });
    }

    /**
     * 📌 Apply sorting options
     */
    private function applySortingApi($query, $sortBy)
    {
        return $query->when($sortBy, function ($query, $sortBy) {
            switch ($sortBy) {
                case 'price_asc':
                    return $query->orderBy('variation_price', 'asc');
                case 'price_desc':
                    return $query->orderBy('variation_price', 'desc');
                case 'created_at_desc':
                    return $query->orderBy('products.created_at', 'desc');
                case 'created_at_asc':
                    return $query->orderBy('products.created_at', 'asc');
                default:
                    return $query->orderBy('products.priority', 'asc');
            }
        });
    }

    /**
     * 📌 Apply customer discount
     */
    private function applyDiscountsApi($product, $request)
    {
        $customerId = Auth::guard('sanctum')->user()->id ?? null;
        $discountDetails = $this->calculateFinalPrice($product, $customerId);

        $product->base_price = $discountDetails['base_price'];
        $product->discount_price = $discountDetails['discount_price'];
        $product->customer_discount_price = $discountDetails['customer_discount_price'];

        return $product;
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

    /**
 * 📌 Search products by name, brand, category, and variations
 */
public function searchProductsApi(Request $request)
{
    $locale = $request->input('lang', app()->getLocale());
    $searchQuery = trim($request->query('q', ''));

    // Base query to get products
    $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
        ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
        ->with([
            'productTranslation', // Load all translations
            'variation.colors.variationColorTranslation',
            'variation.sizes.variationSizeTranslation',
            'variation.materials.variationMaterialTranslation',
            'variation.capacities.variationCapacityTranslation',
            'variation.images' => fn($q) => $q->where(function ($q) {
                $q->where('priority', 0)->orWhere('is_primary', 1);
            }),
            'brand.brandTranslation',
            'categories.categoryTranslation',
        ])
        ->where('products.status', 1)
        ->where('products.is_spare_part', 0);

    // Apply search filters across ALL languages
    if (!empty($searchQuery)) {
        $productQuery->where(function ($query) use ($searchQuery) {
            $query->whereHas('productTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('brand.brandTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('categories.categoryTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.colors.variationColorTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.sizes.variationSizeTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.materials.variationMaterialTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.capacities.variationCapacityTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(keywords) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            });
        });
    }

    // Paginate results
    $products = $productQuery->paginate(10)->appends(['q' => $searchQuery]);

    // Apply discount calculations and format results
    $products->getCollection()->transform(fn($product) => $this->applyDiscountsApi($product, $request));
    $formattedProducts = $products->getCollection()->map(fn($product) => $this->formatProduct($product));

    return response()->json([
        'query' => $searchQuery,
        'products' => $formattedProducts,
        'pagination' => [
            'current_page' => $products->currentPage(),
            'per_page'     => $products->perPage(),
            'total'        => $products->total(),
            'last_page'    => $products->lastPage(),
        ],
    ]);
}

public function searchProductsAllApi(Request $request)
{
    $locale = $request->input('lang', app()->getLocale());
    $searchQuery = trim($request->query('q', ''));

    // Base query to get products
    $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
        ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
        ->with([
            'productTranslation', // Load all translations
            'variation.colors.variationColorTranslation',
            'variation.sizes.variationSizeTranslation',
            'variation.materials.variationMaterialTranslation',
            'variation.capacities.variationCapacityTranslation',
            'variation.images' => fn($q) => $q->where(function ($q) {
                $q->where('priority', 0)->orWhere('is_primary', 1);
            }),
            'brand.brandTranslation',
            'categories.categoryTranslation',
        ])
        ->where('products.is_spare_part', 0);

    // Apply search filters across ALL languages
    if (!empty($searchQuery)) {
        $productQuery->where(function ($query) use ($searchQuery) {
            $query->whereHas('productTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('brand.brandTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('categories.categoryTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.colors.variationColorTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.sizes.variationSizeTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.materials.variationMaterialTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation.capacities.variationCapacityTranslation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(name) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            })
            ->orWhereHas('variation', function ($subQuery) use ($searchQuery) {
                $subQuery->whereRaw("LOWER(keywords) COLLATE utf8mb4_general_ci LIKE LOWER(?)", ["%$searchQuery%"]);
            });
        });
    }

    // Paginate results
    $products = $productQuery->paginate(10)->appends(['q' => $searchQuery]);

    // Apply discount calculations and format results
    $products->getCollection()->transform(fn($product) => $this->applyDiscountsApi($product, $request));
    $formattedProducts = $products->getCollection()->map(fn($product) => $this->formatProduct($product));

    return response()->json([
        'query' => $searchQuery,
        'products' => $formattedProducts,
        'pagination' => [
            'current_page' => $products->currentPage(),
            'per_page'     => $products->perPage(),
            'total'        => $products->total(),
            'last_page'    => $products->lastPage(),
        ],
    ]);
}




}
