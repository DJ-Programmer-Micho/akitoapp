<?php

namespace App\Http\Controllers\Main;

use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\VariationSize;
use App\Models\VariationColor;
use App\Models\ProductVariation;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Cache;

class BusinessController extends Controller
{
    public function home(){
        $productsCat1 = Cache::remember('active_products_category_1', 60, function () {
            return Product::where('status', 1)
                           ->whereHas('categories', function ($query) {
                               // Specify which table's `id` column you are referring to
                               $query->where('categories.id', 4);
                           })
                           ->get();
        });
        $productsCat1Title = "Coffee Makers";

        $productsCat2 = Cache::remember('active_products_category_2', 60, function () {
            return Product::where('status', 1)
                           ->whereHas('categories', function ($query) {
                               // Specify which table's `id` column you are referring to
                               $query->where('categories.id', 1);
                           })
                           ->get();
        });
        $productsCat2Title = "cat1";

        $productsCat3 = Cache::remember('active_products_category_3', 60, function () {
            return Product::where('status', 1)
                           ->whereHas('categories', function ($query) {
                               // Specify which table's `id` column you are referring to
                               $query->where('categories.id', 3);
                           })
                           ->get();
        });
        $productsCat3Title = "BREW";

        return view('mains.pages.home-page-one', [
            'productsCat1' => $productsCat1,
            'productsCat1Title' => $productsCat1Title,
            'productsCat2' => $productsCat2,
            'productsCat2Title' => $productsCat2Title,
            'productsCat3' => $productsCat3,
            'productsCat3Title' => $productsCat3Title,
        ]);
    }

    public function productCategory(){
        return view('mains.pages.category-page-one', [

        ]);
    }

    public function productBrand(){
        return view('mains.pages.brand-page-one', [

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
        $grid = $request->query('grid', 4);
        $currentPage = $request->query('page', 1);
        
        // Convert price filter values to numeric
        $minPrice = floatval(str_replace('$', '', $minPrice));
        $maxPrice = floatval(str_replace('$', '', $maxPrice));
        
        // Base query to get products
        $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
            ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
            ->with([
                'productTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'variation.colors',
                'variation.sizes',
                'variation.materials',
                'variation.capacities',
                'variation.images',
                'brand.brandtranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'categories.categoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'tags.tagTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'subCategories.subCategoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
            ])
            ->where('products.status', 1)
            ->where('products.is_spare_part', 0);
        
        // Apply filters to the query
        $productQuery->when($brandIds, function ($query, $brandIds) {
            return $query->whereIn('products.brand_id', $brandIds);
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
            return $query->whereBetween('product_variations.price', [$minPrice, $maxPrice]);
        });
        
        // Apply sorting
        $productQuery->when($sortBy, function ($query, $sortBy) {
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
                    return $query->orderBy('products.priority', 'asc'); // Default sorting by priority
            }
        });
        
        // Get the filtered products with pagination
        $products = $productQuery->paginate(12);
        
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
            ->with(['brandtranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->get();
        
        $categories = Category::where('status', 1)
            ->whereIn('id', $categoryIdsFromProducts)
            ->with(['categoryTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->get();
        
        $subCategories = SubCategory::where('status', 1)
            ->whereIn('id', $subCategoryIdsFromProducts)
            ->with(['subCategoryTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->get();
        
        $colors = VariationColor::where('status', 1)
            ->whereIn('id', $colorIdsFromProducts)
            ->get();

            $sizes = VariationSize::where('status', 1)
            ->whereIn('id', $sizeIdsFromProducts)
            ->with(['variationSizeTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->get();
        
        $capacities = VariationCapacity::where('status', 1)
            ->whereIn('id', $capacityIdsFromProducts)
            ->with(['variationCapacityTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->get();
        
        $materials = VariationMaterial::where('status', 1)
            ->whereIn('id', $materialIdsFromProducts)
            ->with(['variationMaterialeTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }])
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
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'grid' => $grid
        ]);
    }
    
    
    public function productShopSpare(Request $request)
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
                $grid = $request->query('grid', 4);
                $currentPage = $request->query('page', 1);
                
                // Convert price filter values to numeric
                $minPrice = floatval(str_replace('$', '', $minPrice));
                $maxPrice = floatval(str_replace('$', '', $maxPrice));
                
                // Base query to get products
                $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
                    ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
                    ->with([
                        'productTranslation' => function ($query) {
                            $query->where('locale', app()->getLocale());
                        },
                        'variation.colors',
                        'variation.sizes',
                        'variation.materials',
                        'variation.capacities',
                        'variation.images',
                        'brand.brandtranslation' => function ($query) {
                            $query->where('locale', app()->getLocale());
                        },
                        'categories.categoryTranslation' => function ($query) {
                            $query->where('locale', app()->getLocale());
                        },
                        'tags.tagTranslation' => function ($query) {
                            $query->where('locale', app()->getLocale());
                        },
                        'subCategories.subCategoryTranslation' => function ($query) {
                            $query->where('locale', app()->getLocale());
                        },
                    ])
                    ->where('products.status', 1)
                    ->where('products.is_spare_part', 1);
                
                // Apply filters to the query
                $productQuery->when($brandIds, function ($query, $brandIds) {
                    return $query->whereIn('products.brand_id', $brandIds);
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
                    return $query->whereBetween('product_variations.price', [$minPrice, $maxPrice]);
                });
                
                // Apply sorting
                $productQuery->when($sortBy, function ($query, $sortBy) {
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
                            return $query->orderBy('products.priority', 'asc'); // Default sorting by priority
                    }
                });
                
                // Get the filtered products with pagination
                $products = $productQuery->paginate(12);
                
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
                    ->with(['brandtranslation' => function ($query) {
                        $query->where('locale', app()->getLocale());
                    }])
                    ->get();
                
                $categories = Category::where('status', 1)
                    ->whereIn('id', $categoryIdsFromProducts)
                    ->with(['categoryTranslation' => function ($query) {
                        $query->where('locale', app()->getLocale());
                    }])
                    ->get();
                
                $subCategories = SubCategory::where('status', 1)
                    ->whereIn('id', $subCategoryIdsFromProducts)
                    ->with(['subCategoryTranslation' => function ($query) {
                        $query->where('locale', app()->getLocale());
                    }])
                    ->get();
                
                $colors = VariationColor::where('status', 1)
                    ->whereIn('id', $colorIdsFromProducts)
                    ->get();
        
                    $sizes = VariationSize::where('status', 1)
                    ->whereIn('id', $sizeIdsFromProducts)
                    ->with(['variationSizeTranslation' => function ($query) {
                        $query->where('locale', app()->getLocale());
                    }])
                    ->get();
                
                $capacities = VariationCapacity::where('status', 1)
                    ->whereIn('id', $capacityIdsFromProducts)
                    ->with(['variationCapacityTranslation' => function ($query) {
                        $query->where('locale', app()->getLocale());
                    }])
                    ->get();
                
                $materials = VariationMaterial::where('status', 1)
                    ->whereIn('id', $materialIdsFromProducts)
                    ->with(['variationMaterialeTranslation' => function ($query) {
                        $query->where('locale', app()->getLocale());
                    }])
                    ->get();
        
        return view('mains.pages.product-shop-spare-one', [
            'products' => $products,
            'brands' => $brands,
            'categories' => $categories,
            'subCategory' => $subCategories,
            'sizes' => $sizes,
            'colors' => $colors,
            'capacities' => $capacities,
            'materials' => $materials,
            'minPrice' => $minPrice,
            'maxPrice' => $maxPrice,
            'grid' => $grid
        ]);
    }
    
    
}