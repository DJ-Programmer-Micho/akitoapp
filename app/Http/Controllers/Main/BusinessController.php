<?php

namespace App\Http\Controllers\Main;

use App\Models\Brand;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\ProductVariation;
use App\Models\SubCategory;
use App\Models\VariationCapacity;
use App\Models\VariationColor;
use App\Models\VariationMaterial;
use App\Models\VariationSize;

class BusinessController extends Controller
{
    public function home(){
        return view('mains.pages.home-page-one',[
            
        ]);

    }
    public function productDetail($locale,$slug){
        $product = Product::with([
            'productTranslation', 
            'variation.colors', 
            'variation.sizes.variationSizeTranslation', 
            'variation.materials', 
            'variation.capacities',
            'variation.images',
            'brand.brandTranslation', 
            'categories.categoryTranslation', 
            'tags.tagTranslation',
            'information.informationTranslation'
        ])
        ->whereHas('productTranslation', function($query) use ($locale, $slug) {
            $query->where('slug', $slug)
                  ->where('locale', $locale);
        })
        ->first();

        return view('mains.pages.product-page-one',[
            'product' => $product,
        ]);
    }


    public function productShop(Request $request)
    {
        // Get all active filters from the request
        $brandIds = $request->query('brands', []);
        $categoryIds = $request->query('categories', []);
        $subCategoryIds = $request->query('subcategories', []);
        $sizeIds = $request->query('sizes', []);
        $colorIds = $request->query('colors', []);
        $capacityIds = $request->query('capacities', []);
        $materialIds = $request->query('materials', []);
        $minPrice = $request->query('min_price', 0);
        $maxPrice = $request->query('max_price', 1000);
        $sortBy = $request->query('sortby', 'priority');
        
        // Convert price filter values to numeric
        $minPrice = floatval(str_replace('$', '', $minPrice));
        $maxPrice = floatval(str_replace('$', '', $maxPrice));
        $minProductPrice = 0;
        $maxProductPrice = ProductVariation::max('price') ?? 1500;
        
        // Base query to get products
        $productQuery = Product::with([
            'productTranslation',
            'variation.colors',
            'variation.sizes',
            'variation.materials',
            'variation.capacities',
            'variation.images',
            'brand.brandTranslation',
            'categories.categoryTranslation',
            'tags.tagTranslation',
            'subCategories.subCategoryTranslation',
        ])->where('status', 1);

        // Apply filters to the query
        $productQuery->when($brandIds, function ($query, $brandIds) {
            return $query->whereIn('brand_id', $brandIds);
        })
        ->when($categoryIds, function ($query, $categoryIds) {
            return $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            });
        })
        ->when($subCategoryIds, function ($query, $subCategoryIds) {
            return $query->whereHas('subCategories', function ($q) use ($subCategoryIds) {
                $q->whereIn('sub_category_id', $subCategoryIds);
            });
        })
        ->when($sizeIds, function ($query, $sizeIds) {
            return $query->whereHas('variation.sizes', function ($q) use ($sizeIds) {
                $q->whereIn('variation_size_id', $sizeIds);
            });
        })
        ->when($colorIds, function ($query, $colorIds) {
            return $query->whereHas('variation.colors', function ($q) use ($colorIds) {
                $q->whereIn('variation_color_id', $colorIds);
            });
        })
        ->when($capacityIds, function ($query, $capacityIds) {
            return $query->whereHas('variation.capacities', function ($q) use ($capacityIds) {
                $q->whereIn('variation_capacity_id', $capacityIds);
            });
        })
        ->when($materialIds, function ($query, $materialIds) {
            return $query->whereHas('variation.materials', function ($q) use ($materialIds) {
                $q->whereIn('variation_material_id', $materialIds);
            });
        })
        ->when([$minPrice, $maxPrice], function ($query) use ($minPrice, $maxPrice) {
            return $query->whereHas('variation', function ($q) use ($minPrice, $maxPrice) {
                $q->whereBetween('price', [$minPrice, $maxPrice]);
            });
        });
    
        // Get the filtered products
        $products = $productQuery->get();
        
        // Compute the min and max prices for the price range slider
        
        // Get the available filters
        $brandIdsFromProducts = $products->pluck('brand_id')->unique();
        $categoryIdsFromProducts = $products->flatMap(function ($product) {
            return $product->categories->pluck('id');
        })->unique();
        $subCategoryIdsFromProducts = $products->flatMap(function ($product) {
            return $product->subCategories->pluck('id');
        })->unique();
        $sizeIdsFromProducts = $products->flatMap(function ($product) {
            return $product->variation->sizes->pluck('id');
        })->unique();
        $colorIdsFromProducts = $products->flatMap(function ($product) {
            return $product->variation->colors->pluck('id');
        })->unique();
        $capacityIdsFromProducts = $products->flatMap(function ($product) {
            return $product->variation->capacities->pluck('id');
        })->unique();
        $materialIdsFromProducts = $products->flatMap(function ($product) {
            return $product->variation->materials->pluck('id');
        })->unique();
        
        $brands = Brand::where('status', 1)
            ->whereIn('id', $brandIdsFromProducts)
            ->get();
        
        $categories = Category::where('status', 1)
            ->whereIn('id', $categoryIdsFromProducts)
            ->get();
        
        $subCategories = SubCategory::where('status', 1)
            ->whereIn('id', $subCategoryIdsFromProducts)
            ->get();
        
        $sizes = VariationSize::where('status', 1)
            ->whereIn('id', $sizeIdsFromProducts)
            ->get();
        
        $colors = VariationColor::where('status', 1)
            ->whereIn('id', $colorIdsFromProducts)
            ->get();
        
        $capacities = VariationCapacity::where('status', 1)
            ->whereIn('id', $capacityIdsFromProducts)
            ->get();
        
        $materials = VariationMaterial::where('status', 1)
            ->whereIn('id', $materialIdsFromProducts)
            ->get();
        
        return view('mains.pages.product-shop-one', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'subCategory' => $subCategories,
            'sizes' => $sizes,
            'colors' => $colors,
            'capacities' => $capacities,
            'materials' => $materials,
            'minPrice' => $minProductPrice,
            'maxPrice' => $maxProductPrice,
        ]);
    }
    
}