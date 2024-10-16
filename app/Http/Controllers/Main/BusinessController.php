<?php

namespace App\Http\Controllers\Main;

use App\Models\User;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Models\VariationSize;
use App\Models\PaymentMethods;
use App\Models\VariationColor;
use App\Models\ProductVariation;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Events\EventCustomerOrderCheckout;
use App\Events\EventNotifyCustomerOrderCheckout;
use App\Notifications\NotifyCustomerOrderCheckout;

class BusinessController extends Controller
{
    public function home() {
        $locale = app()->getLocale();  // Get the current locale
    
        // Fetch products for the first category
        $productsCat1 = Cache::remember("active_products_category_1_$locale", 60, function () use ($locale) {
            return Product::with([
                'productTranslation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                'variation.colors',
                'variation.sizes',
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
                }
            ])
            ->where('status', 1)
            ->whereHas('categories', function ($query) {
                $query->where('categories.id', 2);
            })
            ->get();
        });
    
        // $productsCat1Title = $productsCat1->first()->categories->first()->categoryTranslation->title ?? __("messages.category_1_title");
        $productsCat1Title = "Coffee Makers";
    
        // Fetch products for the second category
        $productsCat2 = Cache::remember("active_products_category_2_$locale", 60, function () use ($locale) {
            return Product::with([
                'productTranslation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                'variation.colors',
                'variation.sizes',
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
                }
            ])
            ->where('status', 1)
            ->whereHas('categories', function ($query) {
                $query->where('categories.id', 3);
            })
            ->get();
        });
    
        // $productsCat2Title = $productsCat2->first()->categories->first()->categoryTranslation->title ?? __("messages.category_2_title");
        $productsCat2Title = "Coffee Beans";
    
        // Fetch products for the third category
        $productsCat3 = Cache::remember("active_products_category_3_$locale", 60, function () use ($locale) {
            return Product::with([
                'productTranslation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                },
                'variation.colors',
                'variation.sizes',
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
                }
            ])
            ->where('status', 1)
            ->whereHas('categories', function ($query) {
                $query->where('categories.id', 2);
            })
            ->get();
        });
    
        // $productsCat3Title = $productsCat3->first()->categories->first()->categoryTranslation->title ?? __("messages.category_3_title");
        $productsCat3Title = "Syrup";

        $categoiresData = Category::where('status', 1)->with(['categoryTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])
        ->get();
        
        $sliders = [];
        $sliders = [
            app('cloudfront') . 'web-setting/sliders/slide1.jpg',
            app('cloudfront') . 'web-setting/sliders/slide2.jpg',
            app('cloudfront') . 'web-setting/sliders/slide3.jpg',
        ];
        

        $imagesBanner = [];
        $imagesBanner = [
            app('cloudfront') . 'web-setting/banners/banner7.png',
            app('cloudfront') . 'web-setting/banners/banner8.png',
            app('cloudfront') . 'web-setting/banners/banner9.png',
        ];

        $featured = $this->fetchProducts('featured', 'Featured');
        $on_sale = $this->fetchProducts('on_sale', 'On Sale');

        return view('mains.pages.home-page-one', [
            'productsCat1' => $productsCat1,
            'productsCat1Title' => $productsCat1Title,
            'productsCat2' => $productsCat2,
            'productsCat2Title' => $productsCat2Title,
            'productsCat3' => $productsCat3,
            'productsCat3Title' => $productsCat3Title,
            'categoiresData' => $categoiresData,
            'imageBanner' => $imagesBanner,
            'featured_products' => $featured,
            'on_sale_products' => $on_sale,
            'sliders' => $sliders,
        ]);
    }

    private function fetchProducts($type, $title)
    {
        $locale = app()->getLocale();
        $products = Product::with([
            'productTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'variation.colors',
            'variation.sizes',
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

        return [
            'products' => $products,
            'title' => $title,
        ];
    }
    
    public function aboutus(){
        return view('mains.pages.aboutus-page-one', [

        ]);
    }
    public function contactus(){
        return view('mains.pages.contactus-page-one', [

        ]);
    }
    
    public function faq(){
        return view('mains.pages.faq-page-one', [

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

    public function productDetail($locale, $slug)
    {
        // Fetch the product with its translations and related data
        $product = Product::with([
            'productTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'variation.colors',
            'variation.sizes.variationSizeTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            // 'variation.materials',
            'variation.materials.variationMaterialTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            // 'variation.capacities',
            'variation.capacities.variationCapacityTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'variation.images'=> function ($query) {
                $query->orderBy('priority'); // Order images by priority
            },
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
        ->whereHas('productTranslation', function ($query) use ($locale, $slug) {
            $query->where('slug', urldecode($slug))  // Decode the slug
                  ->where('locale', $locale);
        })
        ->first();
        // Check if the product was found
        if (!$product) {
            // Optionally, you can return a 404 page or redirect
            abort(404, 'Product not found');
        }
    
        // Extract the first translation from the collection
        $product->productTranslation = $product->productTranslation->first();
        $product->information->informationTranslation = $product->information->informationTranslation->first();
    
        // Pass the product and its translation to the view
        return view('mains.pages.product-page-one', [
            'product' => $product,
            'locale' => $locale,
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
            ->with(['variationMaterialTranslation' => function ($query) {
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
                    ->with(['variationMaterialTranslation' => function ($query) {
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

    public function searchShop(Request $request)
    {
        // Get the search query from the request
        $searchQuery = $request->query('q', '');
    
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
            ])
            ->where('products.status', 1)
            ->where('products.is_spare_part', 0);
    
        // Search query logic
        if (!empty($searchQuery)) {
            $productQuery->where(function ($query) use ($searchQuery) {
                // Search in product translation
                $query->whereHas('productTranslation', function ($subQuery) use ($searchQuery) {
                    // Make sure 'name' column exists, or replace 'name' with the correct column
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                // Search in brand translation
                ->orWhereHas('brand.brandTranslation', function ($subQuery) use ($searchQuery) {
                    // Replace 'name' with the correct column if needed
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                // Search in category translation
                ->orWhereHas('categories.categoryTranslation', function ($subQuery) use ($searchQuery) {
                    // Replace 'name' with the correct column if needed
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                // Search in variation colors
                ->orWhereHas('variation.colors.variationColorTranslation', function ($subQuery) use ($searchQuery) {
                    // Replace 'name' with the correct column if needed
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                // Search in variation sizes
                ->orWhereHas('variation.sizes.variationSizeTranslation', function ($subQuery) use ($searchQuery) {
                    // Replace 'name' with the correct column if needed
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                // Search in variation materials
                ->orWhereHas('variation.materials.variationMaterialTranslation', function ($subQuery) use ($searchQuery) {
                    // Replace 'name' with the correct column if needed
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                // Search in variation capacities
                ->orWhereHas('variation.capacities.variationCapacityTranslation', function ($subQuery) use ($searchQuery) {
                    // Replace 'name' with the correct column if needed
                    $subQuery->where('name', 'like', '%' . $searchQuery . '%');
                })
                ->orWhereHas('variation', function ($subQuery) use ($searchQuery) {
                    // Replace 'name' with the correct column if needed
                    $subQuery->where('keywords', 'like', '%' . $searchQuery . '%');
                });
            });
        }
    
        // Get the filtered products with pagination
        $products = $productQuery->paginate(3);
    
        return view('mains.pages.search-page-one', [
            'products' => $products,
            'searchQuery' => $searchQuery,
        ]);
    }
    
    
    public function account(){        
        $isLoggedIn = Auth::guard('customer')->check();
    
        if(!$isLoggedIn){
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }
        return view('mains.pages.account-page-one');
    }

    public function register(){
        return view('mains.pages.register-page-one', [

        ]);
    }

    public function wishlist(){
        $isLoggedIn = Auth::guard('customer')->check();
    
        if(!$isLoggedIn){
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }
        return view('mains.pages.wishlist-page-one', [

        ]);
    }
    
    public function viewcart(){
        $isLoggedIn = Auth::guard('customer')->check();
    
        if(!$isLoggedIn){
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }
        return view('mains.pages.cart-view-page-one', [

        ]);
    }

    public function checkout(){
        $isLoggedIn = Auth::guard('customer')->check();
    
        if(!$isLoggedIn){
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }
        return view('mains.pages.checkout-page-one', [

        ]);
    }

    public function checkoutChecker($locale, $digit, $nvxf, Request $request){
        
        if(Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            if($customer->status == 1 && $customer->id == $nvxf){
                if($digit == 1) {
                    try {
                        //code...
                        $validatedData = $request->validate([
                            'shipping_amount' => 'required|string|max:255',
                            'total_amount' => 'required|string|max:255',
                            'address' => 'required|string|max:255',
                            'payment' => 'required|string|max:255',
                        ]);

                        $customerP = Auth::guard('customer')->user()->customer_profile;
                        $customerA = Auth::guard('customer')->user()->customer_addresses->where('id',$validatedData['address'])->first();
                        $paymentType = PaymentMethods::where('id', $validatedData['payment'])->first()->name;
                        $cartItems = CartItem::with('product','product.variation','product.productTranslation')->where('customer_id',$customer->id)->get();
                        $random_number = Str::random(6);

                        DB::beginTransaction();
                        $order = Order::create([
                            'customer_id' => $customer->id,
                            'first_name' =>  $customerP->first_name,
                            'last_name' => $customerP->last_name,
                            'email' => $customer->email,
                            'country' => $customerA->country,
                            'city' => $customerA->city,
                            'address' => $customerA->address,
                            'zip_code' => $customerA->zip_code,
                            'latitude' => $customerA->latitude,
                            'longitude' => $customerA->longitude,
                            'phone_number' => $customerA->phone_number,
                            'payment_method' => $paymentType,
                            'payment_status' => 'pending',
                            'status' => 'pending', 
                            'tracking_number' => $random_number,
                            'discount' => null, 
                            'shipping_amount' => $validatedData['shipping_amount'], // ***********************
                            'total_amount' => $validatedData['total_amount'] // ***********************
                        ]);

                        foreach ($cartItems as $item) {

                            $pPrice = $item->product->variation->discount ? $item->product->variation->discount : $item->product->variation->price;

                            OrderItem::create([
                                'order_id' => $order->id,
                                'product_id' => $item->product_id, // Assuming you're getting product_id
                                'quantity' => $item->quantity,
                                'product_name' => $item->product->productTranslation[0]->name,
                                'price' => $pPrice,
                                'total' => $item->quantity * $pPrice, // Calculate total for this item
                            ]);
                        }

                        try {
                            $adminUsers = User::whereHas('roles', function ($query) {
                                $query->where('name', 'Administrator')
                                      ->orWhere('name', 'Data Entry Specialist')
                                      ->orWhere('name', 'Finance Manager')
                                      ->orWhere('name', 'Order Processor');
                            })->whereDoesntHave('roles', function ($query) {
                                $query->where('name', 'Driver');
                            })->get();
                
                            foreach ($adminUsers as $admin) {
                                if (!$admin->notifications()->where('data->order_id', $order->tracking_number)
                                    ->where('data->tracking_number', $random_number)->exists()) {
                                    $admin->notify(new NotifyCustomerOrderCheckout(
                                        $order->tracking_number, 
                                        $order->id,
                                        $customerP->first_name .' '. $customerP->last_name, 
                                        "New Order has Been Submitted By {$customerP->first_name} {$customerP->last_name} Order ID: [#{$random_number}]", 
                                    ));
                                }
                            }
                            try {
                            broadcast(new EventCustomerOrderCheckout($random_number, $customerP->first_name .' '. $customerP->last_nam))->toOthers();    
                            } catch (\Exception $e) {
                                // DO NOTHING
                            }
                        } catch (\Exception $e) {
                        }
                        DB::commit();

                        CartItem::where('customer_id', $customer->id)->delete();

                        return redirect()->route('business.account',['locale' => app()->getLocale()]);
                    // return 'Cash On Delivery';
                    } catch (\Exception $e) {
                        DB::rollBack();
                        dd($e);
                    }
                } else {
                    return 'PAYMENT = Digital Payment';
                }
            } else {
                return 'err2';
            } 
        } else {
            return 'err1';
        }
        // return view('mains.pages.checkout-page-one', [

        // ]);
    }
}