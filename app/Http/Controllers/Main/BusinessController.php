<?php

namespace App\Http\Controllers\Main;

use App\Models\Brand;
use App\Models\Order;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\WebSetting;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use App\Models\DiscountRule;
use Illuminate\Http\Request;
use App\Models\VariationSize;
use App\Models\PaymentMethods;
use App\Models\VariationColor;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Services\PaymentServiceManager;


class BusinessController extends Controller
{
    public function home() {
        $locale = app()->getLocale(); // Get the current locale
        $settings = WebSetting::find(1);

        $bannerImages = json_decode($settings->banner_images, true);

        // Initialize an array to store product data by category
        $categoryProducts = [];
        foreach ($bannerImages as $banner) {
            $categoryId = $banner['category_id'];
            $subCategoryId = $banner['sub_category_id'] ?? null;
            $categoryProducts[] = $this->fetchProductsByCategory($categoryId, $subCategoryId, "", $locale);
        }

        // Fetch category data
        $categoriesData = Cache::remember("categories_data_$locale", 60, function () use ($locale) {
            return Category::where('status', 1)
                ->with(['categoryTranslation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                }])
                ->orderBy('priority', 'ASC')
                ->get();
        });

        // Slider and Banner images
        $heroImages = json_decode($settings->hero_images, true); // true for associative array

        // Collect images based on locale
        $sliders = collect($heroImages[$locale] ?? []);
    
        // Sort by priority, ensuring that priority is treated as an integer
        $sortedSliders = $sliders->sortBy(function ($item) {
            return (int) $item['priority']; // Cast priority to integer
        })->values()->all();
        // $sliders = $this->getSliderImages();
        // $imagesBanner = $this->getBannerImages();

        // Featured and On Sale products
        $featured = $this->fetchProducts('featured', 'Featured');
        $on_sale = $this->fetchProducts('on_sale', 'On Sale');

        // dd($categoryProducts, $categoriesData);
        return view('mains.pages.home-page-one', [
            'categoryProducts' => $categoryProducts,
            'categoriesData' => $categoriesData,
            'imageBanner' => $bannerImages,
            'featured_products' => $featured,
            'on_sale_products' => $on_sale,
            'sliders' => $sortedSliders,
        ]);
    }

    private function fetchProductsByCategory($categoryId, $subCategoryId = null, $defaultTitle, $locale)
    {
        $customerId = auth('customer')->user()->id ?? null; // Assuming customer is logged in
        return Cache::remember("active_products_category_{$categoryId}_{$subCategoryId}_$locale", 60, function () use ($categoryId, $subCategoryId, $locale, $defaultTitle, $customerId) {
            $products = Product::with($this->productRelationships($locale))
                ->where('status', 1)
                ->whereHas('categories', function ($query) use ($categoryId) {
                    $query->where('categories.id', $categoryId);
                })
                ->when($subCategoryId, function ($query) use ($subCategoryId) {
                    $query->whereHas('subCategories', function ($subQuery) use ($subCategoryId) {
                        $subQuery->where('sub_categories.id', $subCategoryId);
                    });
                })
                ->orderBy('priority', 'ASC')
                ->get()
                ->map(function ($product) use ($customerId) {
                    $finalPrices = $this->calculateFinalPrice($product, $customerId);
                    $product->base_price = $finalPrices['base_price'];
                    $product->discount_price = $finalPrices['discount_price'];
                    $product->customer_discount_price = $finalPrices['customer_discount_price'];
                    return $product;
                });
    
            $firstProduct = $products->first();
            $categoryTitle = $firstProduct?->categories->first()?->categoryTranslation->title ?? $defaultTitle;
    
            return [
                'products' => $products,
                'title' => $categoryTitle,
            ];
        });
    }

    private function productRelationships($locale)
    {
        return [
            'productTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'variation.colors',
            'variation.sizes',
            'variation.materials',
            'variation.capacities',
            'variation.images' => function ($query) {
                // Here you can filter the images based on your requirements
                $query->where(function ($query) {
                    $query->where('priority', 0)
                          ->orWhere('is_primary', 1);
                });
            },
            'brand.brandTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'categories.categoryTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'subCategories.subCategoryTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            },
            'tags.tagTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }
        ];
    }

    private function fetchProducts($type, $title)
    {
        $locale = app()->getLocale();
        $customerId = auth('customer')->check() ? auth('customer')->user()->id : null; // Get customer ID
        
        $products = Product::with($this->productRelationships($locale))
            ->where('status', 1)
            ->whereHas('variation', function ($query) use ($type) {
                $query->where($type, 1);
            })
            ->get()
            ->map(function ($product) use ($customerId) {
                // Calculate prices and store them in the product object
                $finalPrices = $this->calculateFinalPrice($product, $customerId);
                $product->base_price = $finalPrices['base_price'];
                $product->discount_price = $finalPrices['discount_price'];
                $product->customer_discount_price = $finalPrices['customer_discount_price'];
                return $product;
            });
    
        return [
            'products' => $products,
            'title' => $title,
        ];
    }
    
    private function calculateFinalPrice($product, $customerId) {
        // Use the original price and discount price if available
        $basePrice = $product->variation->price; // Original price
        $discountPrice = $product->variation->discount ?? $basePrice; // Use discounted price if applicable

        // Initialize total discount percentage
        $totalDiscountPercentage = 0;

        // Check for applicable discounts
    
        if ($customerId) {
            // Fetch discount rules for the customer
            $discountRules = DiscountRule::where('customer_id', $customerId)
                ->where(function ($query) use ($product) {
                    $query->where('product_id', $product->id)
                        ->orWhere('category_id', $product->categories->first()->id)
                        ->orWhere('sub_category_id',$product->subCategories->first()->id) // Assuming you have this relation
                        ->orWhere('brand_id', $product->brand_id);
                })
                ->get();

            // Iterate through the discount rules and accumulate applicable discounts
            foreach ($discountRules as $rule) {
                // Sum discounts for the same product, brand, category, and subcategory
                if ($rule->product_id === $product->id && $rule->type === 'product') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Product discount
                }

                if ($rule->brand_id === $product->brand_id && $rule->type === 'brand') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Brand discount
                }

                if ($rule->category_id === $product->categories->first()->id && $rule->type === 'category') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Category discount
                }

                if ($rule->sub_category_id === $product->subCategories->first()->id && $rule->type === 'subcategory') {
                    $totalDiscountPercentage += (float) $rule->discount_percentage; // Subcategory discount
                }
            }
        }

        // Ensure the total discount percentage does not exceed 100%
        $totalDiscountPercentage = min($totalDiscountPercentage, 100);

        // Calculate the final customer discount price based on the total applicable discounts
        $customerDiscountPrice = $discountPrice * (1 - ($totalDiscountPercentage / 100));
        return [
            'base_price' => $basePrice,
            'discount_price' => $discountPrice,
            'customer_discount_price' => $customerDiscountPrice,
            'total_discount_percentage' => $totalDiscountPercentage
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

    public function productDetail($locale, $slug, Request $request)
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
            'variation.intensity' => function ($query) use ($locale) {
                
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
            'subCategories.subCategoryTranslation' => function ($query) use ($locale) {
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
    
        // Calculate discount details for the product
        $customerId = request()->user('customer')->id ?? null;
        $discountDetails = $this->calculateFinalPrice($product, $customerId);

        // Assign the calculated prices to the product
        $product->base_price = $discountDetails['base_price'];
        $product->discount_price = $discountDetails['discount_price'];
        $product->customer_discount_price = $discountDetails['customer_discount_price'];
        $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
            
        $seo = [
            'title' => $product->productTranslation->name ?? 'Akitu Product',
            'description' => Str::limit($product->productTranslation->description, 160) ?? 'Akitu Product',
            'keywords' => Str::limit($product->variation->keywords, 160) ?? 'akitu, coffee shop',
            'image' => app('cloudfront').$product->variation->images[0]->image_path ?? 'default_image_url',
        ];

        // Pass the product and its translation to the view
        return view('mains.pages.product-page-one', [
            'seo' => $seo,
            'product' => $product,
            'locale' => $locale,
        ]);
    }
    
    public function productShop(Request $request)
    {
        // Get filters and defaults
        $filters = $this->getFiltersFromRequest($request);
        $filters['is_spare_part'] = 0; // Non-spare parts

        // Base query to get products
        $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
            ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
            ->with($this->getEagerLoadRelations($filters))
            ->where('products.status', 1)
            ->where('products.is_spare_part', 0);
    
        // Apply filters
        $productQuery = $this->applyFilters($productQuery, $filters);
        // Apply sorting
        $productQuery = $this->applySorting($productQuery, $filters['sortBy']);
        // Paginate products
        $products = $productQuery->paginate(12);
        // Apply final price calculation
        $products->getCollection()->transform(fn($product) => $this->applyDiscounts($product, $request));
        // Queries for filters (categories, brands, subcategories, etc.)
        $filterQueries = $this->getFilterQueries($filters['categoryIds'], 0);
    
        return view('mains.pages.product-shop-one', array_merge($filterQueries, [
            'products' => $products,
            'minPrice' => $filters['minPrice'],
            'maxPrice' => $filters['maxPrice'],
            'grid' => $filters['grid'],
        ]));
    }

    public function productShopSpare(Request $request)
    {
        // Get filters and defaults
        $filters = $this->getFiltersFromRequest($request);
        $filters['is_spare_part'] = 1; // Non-spare parts

        // Base query to get products
        $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
            ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
            ->with($this->getEagerLoadRelations($filters))
            ->where('products.status', 1)
            ->where('products.is_spare_part', 1);
    
        // Apply filters
        $productQuery = $this->applyFilters($productQuery, $filters);
        // Apply sorting
        $productQuery = $this->applySorting($productQuery, $filters['sortBy']);
        // Paginate products
        $products = $productQuery->paginate(12);
        // Apply final price calculation
        $products->getCollection()->transform(fn($product) => $this->applyDiscounts($product, $request));
        // Queries for filters (categories, brands, subcategories, etc.)
        $filterQueries = $this->getFilterQueries($filters['categoryIds'], 1);
    
        return view('mains.pages.product-shop-one', array_merge($filterQueries, [
            'products' => $products,
            'minPrice' => $filters['minPrice'],
            'maxPrice' => $filters['maxPrice'],
            'grid' => $filters['grid'],
        ]));
    }

    
    private function getFiltersFromRequest(Request $request)
    {
        return [
            'brandIds' => $request->query('brands', []),
            'categoryIds' => $request->query('categories', []),
            'subCategoryIds' => $request->query('subcategories', []),
            'sizeIds' => $request->query('sizes', []),
            'colorIds' => $request->query('colors', []),
            'capacityIds' => $request->query('capacities', []),
            'materialIds' => $request->query('materials', []),
            'minPrice' => floatval($request->query('min_price', 0)),
            'maxPrice' => floatval($request->query('max_price', 5000)),
            'sortBy' => $request->query('sortby', 'priority'),
            'grid' => $request->query('grid', 4),
        ];
    }
    
    private function getEagerLoadRelations($filters)
    {
        $relations = [
            'productTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            },
            'variation.colors', 'variation.sizes', 'variation.materials', 'variation.capacities', 'variation.images',
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
        ];
    
        return $relations;
    }
    
    private function applyFilters($query, $filters)
    {
        $query = $query->when($filters['is_spare_part'] !== null, fn($query) =>
            $query->where('products.is_spare_part', $filters['is_spare_part'])
        );
        
        return $query->when($filters['categoryIds'], fn($query) =>
                $query->whereHas('categories', fn($q) => $q->whereIn('category_id', $filters['categoryIds']))
            )
            ->when($filters['brandIds'], fn($query) =>
                $query->whereIn('products.brand_id', $filters['brandIds'])
            ) // Add this condition for brand filter
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
            ->when([$filters['minPrice'], $filters['maxPrice']], fn($query) =>
                $query->whereBetween('product_variations.price', [$filters['minPrice'], $filters['maxPrice']])
            );
    }
    
    
    private function applySorting($query, $sortBy)
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
    
    private function applyDiscounts($product, $request)
    {
        $discountDetails = $this->calculateFinalPrice($product, $request->user('customer')->id ?? null);
        $product->base_price = $discountDetails['base_price'];
        $product->discount_price = $discountDetails['discount_price'];
        $product->customer_discount_price = $discountDetails['customer_discount_price'];
        $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
        return $product;
    }
    
    private function getFilterQueries($categoryIds, $sparepart)
{
    return [
        'categories' => Category::where('status', 1)
            ->with(['categoryTranslation' => function($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->whereHas('product', function($query) use ($sparepart) { // Use $sparepart here
                $query->where('is_spare_part', $sparepart);
            })
            ->orderBy("priority","asc")
            ->get(),

        'brands' => Brand::where('status', 1)
            ->whereHas('product', function($q) use ($sparepart) { // Use $sparepart here
                $q->where('is_spare_part', $sparepart);
            })
            ->with(['brandtranslation' => function($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->orderBy("priority","asc")
            ->get(),

        'subCategories' => SubCategory::where('status', 1)
            ->when($categoryIds, function($query) use ($categoryIds) {
                $query->whereHas('product.categories', function($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds);
                });
            })
            ->whereHas('product', function($q) use ($sparepart) { // Use $sparepart here
                $q->where('is_spare_part', $sparepart);
            })
            ->with(['subCategoryTranslation' => function($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->orderBy("priority","asc")
            ->get(),

        'sizes' => VariationSize::where('status', 1)
            ->when($categoryIds, function($query) use ($categoryIds) {
                $query->whereHas('productVariations.product.categories', function($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds);
                });
            })
            ->whereHas('productVariations.product', function($q) use ($sparepart) { // Use $sparepart here
                $q->where('is_spare_part', $sparepart);
            })
            ->with(['variationSizeTranslation' => function($query) {
                $query->where('locale', app()->getLocale());
            }])
            ->orderBy("priority","asc")
            ->get(),

        'colors' => VariationColor::where('status', 1)
            ->when($categoryIds, function($query) use ($categoryIds) {
                $query->whereHas('productVariations.product.categories', function($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds);
                });
            })
            ->whereHas('productVariations.product', function($q) use ($sparepart) { // Use $sparepart here
                $q->where('is_spare_part', $sparepart);
            })
            ->get(),

        'capacities' => VariationCapacity::where('status', 1)
            ->when($categoryIds, function($query) use ($categoryIds) {
                $query->whereHas('productVariations.product.categories', function($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds);
                });
            })
            ->whereHas('productVariations.product', function($q) use ($sparepart) { // Use $sparepart here
                $q->where('is_spare_part', $sparepart);
            })
            ->orderBy("priority","asc")
            ->get(),

        'materials' => VariationMaterial::where('status', 1)
            ->when($categoryIds, function($query) use ($categoryIds) {
                $query->whereHas('productVariations.product.categories', function($q) use ($categoryIds) {
                    $q->whereIn('category_id', $categoryIds);
                });
            })
            ->whereHas('productVariations.product', function($q) use ($sparepart) { // Use $sparepart here
                $q->where('is_spare_part', $sparepart);
            })
            ->orderBy("priority","asc")
            ->get(),
    ];
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
                'variation.images' => function ($query) {
                    // Here you can filter the images based on your requirements
                    $query->where(function ($query) {
                        $query->where('priority', 0)
                              ->orWhere('is_primary', 1);
                    });
                },
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
        $products = $productQuery->paginate(10)->appends(['q' => $searchQuery]);

        $products->getCollection()->transform(function ($product) use ($request) {
            $discountDetails = $this->calculateFinalPrice($product, $request->user('customer')->id ?? null);
            $product->base_price = $discountDetails['base_price'];
            $product->discount_price = $discountDetails['discount_price'];
            $product->customer_discount_price = $discountDetails['customer_discount_price'];
            $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
            return $product;
        });
    
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

    
    public function checkoutChecker($locale, $digit, $nvxf, Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customer = Auth::guard('customer')->user();
        if ($customer->status != 1 || $customer->id != $nvxf) {
            return response()->json(['error' => 'Invalid customer'], 403);
        }

        try {
        // ✅ Validate Request Data
        $validatedData = $request->validate([
            'shipping_amount' => 'required|string|max:255',
            'total_amount' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'payment' => 'required|integer|exists:payment_methods,id',
        ]);

        // ✅ Retrieve Active Payment Method
        $paymentMethod = PaymentMethods::where('id', $validatedData['payment'])
            ->where('active', 1)
            ->first();

        if (!$paymentMethod) {
            return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                ->withErrors(['payment' => 'Selected payment method is not available.']);
        }

        // ✅ Retrieve Customer Details
        $customerP = $customer->customer_profile;
        $customerA = $customer->customer_addresses->where('id', $validatedData['address'])->first();
        $trackingNumber = Str::random(6);

        // ✅ Retrieve Cart Items & Apply Discounts
        $cartItems = CartItem::with('product', 'product.variation', 'product.productTranslation')
            ->where('customer_id', $customer->id)
            ->get()
            ->transform(function ($item) use ($customer) {
                $product = $item->product;
                $discountDetails = $this->calculateFinalPrice($product, $customer->id);
                $item->final_price = $discountDetails['customer_discount_price'] ?? $discountDetails['discount_price'] ?? $discountDetails['base_price'];
                return $item;
            });

        DB::beginTransaction();

        // ✅ Create Order
        $order = Order::create([
            'customer_id' => $customer->id,
            'first_name' => $customerP->first_name,
            'last_name' => $customerP->last_name,
            'email' => $customer->email,
            'country' => $customerA->country,
            'city' => $customerA->city,
            'address' => $customerA->address,
            'zip_code' => $customerA->zip_code,
            'latitude' => $customerA->latitude,
            'longitude' => $customerA->longitude,
            'phone_number' => $customerA->phone_number,
            'payment_method' => $paymentMethod->name,
            'payment_status' => 'pending',
            'status' => 'pending',
            'tracking_number' => $trackingNumber,
            'shipping_amount' => $validatedData['shipping_amount'],
            'total_amount' => $validatedData['total_amount']
        ]);

        // ✅ Store Order Items
        foreach ($cartItems as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item->product_id,
                'quantity' => $item->quantity,
                'product_name' => $item->product->productTranslation[0]->name,
                'price' => $item->final_price,
                'total' => $item->quantity * $item->final_price,
            ]);
        }

        // ✅ Handle Cash On Delivery
        if ($paymentMethod->online == 0) {
            DB::commit();
            CartItem::where('customer_id', Auth::guard('customer')->id())->delete();
            return redirect()->route('business.checkout.success', ['locale' => app()->getLocale()]);
        }

            $paymentService = PaymentServiceManager::getInstance()
                ->setOrder($order)
                ->setPaymentMethod($paymentMethod->name)
                ->setAmount($order->total_amount);
                
            $paymentResponse = $paymentService->processPayment();

            if (!$paymentResponse) {
                // Log::error("PaymentServiceManager instance is null!");
                DB::rollBack();
                return redirect()->route('digit.payment.error', ['locale' => app()->getLocale()]);
            }
            DB::commit();
            CartItem::where('customer_id', $customer->id)->delete();
            return redirect()->route('payment.process', 
            ['locale' => app()->getLocale(), 'orderId' => $order->id,'paymentId' => $paymentResponse['paymentId'], 'paymentMethod' => $digit]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout Error: " . $e->getMessage());
            // return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
            //     ->withErrors(['error' => 'Payment processing failed.']);
        }
    }

    public function checkoutExistingOrder($locale, $digit, $orderId, $grandTotalUpdated, Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customer = Auth::guard('customer')->user();

        try {
            // ✅ Retrieve Existing Order
            $order = Order::where('id', $orderId)
                ->where('customer_id', $customer->id)
                ->whereIn('payment_status', ['pending', 'failed']) // Correct
                ->first();
            if (!$order) {
                return response()->json(['error' => 'Order not found or already processed'], 404);
            }

            // ✅ Retrieve Active Payment Method
            $paymentMethod = PaymentMethods::where('id',$digit)
                ->where('active', 1)
                ->first();

            if (!$paymentMethod) {
                return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                    ->withErrors(['payment' => 'Selected payment method is not available.']);
            }
            DB::beginTransaction();

            // ✅ Update `updated_at` timestamp
            $order->touch();

            // ✅ Process Payment
            if ($paymentMethod->online == 0) {
                DB::commit();
                CartItem::where('customer_id', Auth::guard('customer')->id())->delete();
                return redirect()->route('business.checkout.success', ['locale' => app()->getLocale()]);
            }
            $paymentService = PaymentServiceManager::getInstance()
                ->setOrder($order)
                ->setPaymentMethod($paymentMethod->name)
                ->setAmount($grandTotalUpdated);
            
            $paymentResponse = $paymentService->processPayment();
                // dd($paymentResponse);

            if (!$paymentResponse) {
                Log::error("PaymentServiceManager instance is null!");
                return redirect()->route('digit.payment.error', ['locale' => app()->getLocale()]);
            }

            DB::commit();

            return redirect()->route('payment.process', [
                'locale' => app()->getLocale(),
                'orderId' => $order->id,
                'paymentId' => $paymentResponse['paymentId'],
                'paymentMethod' => $digit
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Checkout Existing Order Error: " . $e->getMessage());
            return response()->json(['error' => 'An error occurred while processing the order'], 500);
        }
    }
    
    public function checkoutOrder($locale, $orderId)
    {
        $isLoggedIn = Auth::guard('customer')->check();
        if (!$isLoggedIn) {
            return redirect()->route('business.home', ['locale' => $locale]);
        }

        $order = Order::where('id', $orderId)
            ->where('customer_id', Auth::guard('customer')->id())
            // ->where('payment_status', 'pending') // Ensure it's unpaid
            ->first();

        if (!$order) {
            return redirect()->route('business.checkout.failed', ['locale' => $locale])
                ->withErrors(['error' => 'Order not found or already paid.']);
        }

        // Fetch available payment methods
        $paymentList = PaymentMethods::where('active', 1)->get();

        // Pass order details to the checkout page
        return view('mains.pages.checkout-page-old-one', compact('order', 'paymentList'));
    }
    
    public function checkSuccess(){
        return view('mains.components.livewire.aftercheckout.check-success', [
            
        ]);
    }
    public function checkFaild(){
        return view('mains.components.livewire.aftercheckout.check-failed', [
            
        ]);
    }
}