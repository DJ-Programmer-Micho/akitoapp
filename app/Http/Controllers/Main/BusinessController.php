<?php

namespace App\Http\Controllers\Main;

use Carbon\Carbon;
use App\Models\Brand;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\CartItem;
use App\Models\Category;
use App\Models\OrderItem;
use App\Models\ComingSoon;
use App\Models\WebSetting;
use App\Models\SubCategory;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Models\DiscountRule;
use App\Models\PhenixSystem;
use Illuminate\Http\Request;
use App\Models\VariationSize;
use App\Models\PaymentMethods;
use App\Models\VariationColor;
use App\Services\WalletService;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use App\Services\PhenixApiService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Mail\EmailInvoiceActionMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Services\PaymentServiceManager;


class BusinessController extends Controller
{
    public $exchange_rate;
    public function __construct()
    {
        $this->exchange_rate = config('currency.exchange_rate');
    }

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

        $soons = Cache::remember("active_coming_soon_$locale", 60, function () use ($locale) {
            return ComingSoon::with([
                'coming_soon_translation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                }
            ])
            ->where('status', 1)
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
            'soons' => $soons,
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
            // 'base_price' => $basePrice * $this->exchange_rate,
            // 'discount_price' => $discountPrice * $this->exchange_rate,
            // 'customer_discount_price' => $customerDiscountPrice * $this->exchange_rate,
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

    public function productSoon(){
        return view('mains.pages.coming-soon-page-one', [

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
            'maxPrice' => floatval($request->query('max_price', 5000000)),
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
                // $query->whereBetween('product_variations.price', [$filters['minPrice'] / $this->exchange_rate, $filters['maxPrice'] / $this->exchange_rate])
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
            $validated = $request->validate([
                'shipping_amount' => 'required|string|max:255',
                'total_amount'    => 'required|string|max:255',
                'address'         => 'required|string|max:255',
                'payment'         => 'required|integer|exists:payment_methods,id',
            ]);

            // Normalize totals (IQD minor units)
            $shippingMinor = $this->toMinor($validated['shipping_amount']);
            $grandMinor    = $this->toMinor($validated['total_amount']);
            $pureMinor     = $grandMinor - $shippingMinor; // items only

            // ✅ Retrieve Active Payment Method
            $paymentMethod = PaymentMethods::where('id', $validated['payment'])
                ->where('active', 1)
                ->first();

            if (!$paymentMethod) {
                return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                    ->withErrors(['payment' => 'Selected payment method is not available.']);
            }

            // ✅ Retrieve Customer Details
            $customerP = $customer->customer_profile;
            $customerA = $customer->customer_addresses->where('id', $validated['address'])->first();
            $trackingNumber = Str::random(6);

            // ✅ Retrieve Cart Items & Apply Discounts
            $cartItems = CartItem::with('product', 'product.variation', 'product.productTranslation', 'product.categories', 'product.subCategories')
                ->where('customer_id', $customer->id)
                ->get()
                ->transform(function ($item) use ($customer) {
                    $product = $item->product;

                    $discountDetails = $this->calculateFinalPrice($product, $customer->id);

                    $base  = $discountDetails['base_price']; // original
                    $final = $discountDetails['customer_discount_price']
                        ?? $discountDetails['discount_price']
                        ?? $discountDetails['base_price'];

                    // store on item so Phenix mapping is accurate
                    $item->base_price  = $base;
                    $item->final_price = $final;

                    return $item;
                });


            DB::beginTransaction();

            // ✅ Create Order
            $order = Order::create([
                'customer_id'      => $customer->id,
                'first_name'       => $customerP->first_name,
                'last_name'        => $customerP->last_name,
                'email'            => $customer->email,
                'country'          => $customerA->country,
                'city'             => $customerA->city,
                'address'          => $customerA->address,
                'zip_code'         => $customerA->zip_code,
                'latitude'         => $customerA->latitude,
                'longitude'        => $customerA->longitude,
                'phone_number'     => $customerA->phone_number,

                'payment_method'   => $paymentMethod->name,
                'payment_status'   => 'pending',
                'status'           => 'pending',
                'tracking_number'  => $trackingNumber,

                'shipping_amount'  => $shippingMinor,
                'total_amount_iqd' => $pureMinor,
                'total_amount_usd' => ($this->exchange_rate > 0)
                    ? round(($pureMinor + $shippingMinor) / $this->exchange_rate, 3)
                    : 0,
                'exchange_rate'    => $this->exchange_rate ?? 1500,

                'total_minor'      => $pureMinor + $shippingMinor,
                'paid_minor'       => 0,
                'refunded_minor'   => 0,
                'currency'         => 'IQD',
            ]);

            // ✅ Store Order Items
            foreach ($cartItems as $item) {
                $lineIQD = $this->toMinor($item->final_price);
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'quantity'     => $item->quantity,
                    'product_name' => $item->product->productTranslation[0]->name,
                    'price_usd'    => 0,
                    'total_usd'    => 0,
                    'price_iqd'    => $lineIQD,
                    'total_iqd'    => $item->quantity * $lineIQD,
                ]);
            }
            $this->sendBillToPhenix(app(PhenixApiService::class), $order, $cartItems);

            // ======================
            // TENDER-SPECIFIC LOGIC
            // ======================

            // ✅ Cash On Delivery (offline)
            if ((int)$paymentMethod->online === 0 && strtolower($paymentMethod->name) !== 'wallet') {
                DB::commit();
                CartItem::where('customer_id', $customer->id)->delete();
                Mail::to($order->customer->email)->queue(new EmailInvoiceActionMail($order));

                return redirect()->route('business.checkout.success', ['locale' => app()->getLocale()]);
            }

            // ✅ Wallet (online=1 but special method)
            if (strtolower($paymentMethod->name) === 'wallet') {
                $wallet = $customer->wallet()->lockForUpdate()->firstOrCreate(['currency' => 'IQD']);
                $totalToPay = $order->total_minor ?? ($pureMinor + $shippingMinor);

                if ($wallet->balance_minor < $totalToPay) {
                    DB::rollBack();
                    return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                        ->withErrors(['wallet' => __('Insufficient wallet balance')]);
                }

                $payment = Payment::create([
                    'order_id'           => $order->id,
                    'customer_id'        => $customer->id,
                    'amount_minor'       => $totalToPay,
                    'currency'           => 'IQD',
                    'method'             => 'Wallet',
                    'status'             => 'successful',
                    'provider'           => 'Wallet',
                    'provider_payment_id'=> null,
                    'idempotency_key'    => Str::uuid(),
                    'type'               => 'order',
                    'meta'               => [],
                ]);

                app(WalletService::class)->debit($wallet, $totalToPay, [
                    'reason' => 'wallet_payment',
                    'meta'   => [
                        'order_id'  => $order->id,
                        'tracking'  => $order->tracking_number,
                        'payment_id'=> $payment->id,
                    ],
                ]);

                $order->paid_minor     = ($order->paid_minor ?? 0) + $totalToPay;
                $order->payment_status = 'successful';
                $order->save();

                DB::commit();
                CartItem::where('customer_id', $customer->id)->delete();
                Mail::to($order->customer->email)->queue(new EmailInvoiceActionMail($order));

                return redirect()->route('business.checkout.success', ['locale' => app()->getLocale()]);
            }

            // ✅ Online Gateways (FIB / ZainCash / Areeba / Stripe)
            $totalToPay = $order->total_minor ?? ($pureMinor + $shippingMinor);

            $payment = Payment::create([
                'order_id'           => $order->id,
                'customer_id'        => $customer->id,
                'amount_minor'       => $totalToPay,
                'currency'           => 'IQD',
                'method'             => $paymentMethod->name,
                'status'             => 'pending',
                'provider'           => $paymentMethod->name,
                'provider_payment_id'=> null,          // 👈 will be filled right after
                'idempotency_key'    => Str::uuid(),
                'type'               => 'order',
                'meta'               => [],
            ]);

            $paymentResponse = PaymentServiceManager::getInstance()
                ->setOrder($order)
                ->setPaymentId($payment->id)
                ->setPaymentMethod($paymentMethod->name)
                ->setAmount($totalToPay)
                ->setDelivery($shippingMinor)
                ->processPayment();

            if (!$paymentResponse || empty($paymentResponse['paymentId'])) {
                Log::error("Gateway init failed");
                DB::rollBack();
                return redirect()->route('digit.payment.error', ['locale' => app()->getLocale()]);
            }

            // 🔑 store provider payment id (FIB paymentId)
            $payment->update([
                'provider_payment_id' => $paymentResponse['paymentId'],
            ]);

            DB::commit();
            CartItem::where('customer_id', $customer->id)->delete();
            Mail::to($order->customer->email)->queue(new EmailInvoiceActionMail($order));

            return redirect()->route('payment.process', [
                'locale'        => app()->getLocale(),
                'orderId'       => $order->id,
                'paymentId'     => $paymentResponse['paymentId'] ?? null,
                'paymentMethod' => $paymentMethod->id,
            ]);

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error("Checkout Error: " . $e->getMessage());
            return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                ->withErrors(['error' => 'Payment processing failed.']);
        }
    }

    public function checkoutExistingOrder($locale, $digit, $orderId, $grandTotalUpdated, Request $request)
    {
        if (!Auth::guard('customer')->check()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $customer = Auth::guard('customer')->user();

        try {
            $order = Order::where('id', $orderId)
                ->where('customer_id', $customer->id)
                ->whereIn('payment_status', ['pending', 'failed'])
                ->first();

            if (!$order) {
                return response()->json(['error' => 'Order not found or already processed'], 404);
            }

            $paymentMethod = PaymentMethods::where('id', $digit)
                ->where('active', 1)
                ->first();

            if (!$paymentMethod) {
                return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                    ->withErrors(['payment' => 'Selected payment method is not available.']);
            }

            DB::beginTransaction();

            // 🔢 ALWAYS trust the order totals from DB, not the URL param
            $shippingMinor = (int) ($order->shipping_amount ?? 0);                  // already in IQD
            $itemsMinor    = (int) ($order->total_amount_iqd ?? 0);                 // IQD
            $grandMinor    = (int) ($order->total_minor ?: ($itemsMinor + $shippingMinor));

            // keep order in sync
            $order->total_minor    = $grandMinor;
            $order->payment_method = $paymentMethod->name;
            $order->touch();
            $order->save();

            // COD again
            if ((int)$paymentMethod->online === 0 && strtolower($paymentMethod->name) !== 'wallet') {
                DB::commit();
                CartItem::where('customer_id', $customer->id)->delete();

                return redirect()->route('business.checkout.success', ['locale' => app()->getLocale()]);
            }

            // Wallet re-pay
            if (strtolower($paymentMethod->name) === 'wallet') {
                $wallet     = $customer->wallet()->lockForUpdate()->firstOrCreate(['currency' => 'IQD']);
                $totalToPay = $grandMinor;

                if ($wallet->balance_minor < $totalToPay) {
                    DB::rollBack();
                    return redirect()->route('business.checkout.failed', ['locale' => app()->getLocale()])
                        ->withErrors(['wallet' => __('Insufficient wallet balance')]);
                }

                $payment = Payment::create([
                    'order_id'           => $order->id,
                    'customer_id'        => $customer->id,
                    'amount_minor'       => $totalToPay,
                    'currency'           => 'IQD',
                    'method'             => 'Wallet',
                    'status'             => 'successful',
                    'provider'           => 'Wallet',
                    'provider_payment_id'=> null,
                    'idempotency_key'    => Str::uuid(),
                    'type'               => 'order',
                    'meta'               => [],
                ]);

                app(WalletService::class)->debit($wallet, $totalToPay, [
                    'reason' => 'wallet_payment',
                    'meta'   => [
                        'order_id'  => $order->id,
                        'tracking'  => $order->tracking_number,
                        'payment_id'=> $payment->id,
                    ],
                ]);

                $order->paid_minor     = ($order->paid_minor ?? 0) + $totalToPay;
                $order->payment_status = 'successful';
                $order->save();

                DB::commit();
                CartItem::where('customer_id', $customer->id)->delete();

                return redirect()->route('business.checkout.success', ['locale' => app()->getLocale()]);
            }

            // 🔌 Online provider retry (FIB / Areeba / ZainCash / Stripe)
            $totalToPay = $grandMinor;

            $payment = Payment::create([
                'order_id'           => $order->id,
                'customer_id'        => $customer->id,
                'amount_minor'       => $totalToPay,
                'currency'           => 'IQD',
                'method'             => $paymentMethod->name,
                'status'             => 'pending',
                'provider'           => $paymentMethod->name,
                'provider_payment_id'=> null,
                'idempotency_key'    => Str::uuid(),
                'type'               => 'order',
                'meta'               => [],
            ]);

            $paymentResponse = PaymentServiceManager::getInstance()
                ->setOrder($order)
                ->setPaymentId($payment->id)
                ->setPaymentMethod($paymentMethod->name)
                ->setAmount($totalToPay)         // ✅ 60600 IQD here, not 43
                ->setDelivery($shippingMinor)
                ->processPayment();

            if (!$paymentResponse || empty($paymentResponse['paymentId'])) {
                DB::rollBack();
                return redirect()->route('digit.payment.error', ['locale' => app()->getLocale()]);
            }

            // store FIB payment id so status endpoint can match
            $payment->update([
                'provider_payment_id' => $paymentResponse['paymentId'],
            ]);

            DB::commit();

            return redirect()->route('payment.process', [
                'locale'        => app()->getLocale(),
                'orderId'       => $order->id,
                'paymentId'     => $paymentResponse['paymentId'],
                'paymentMethod' => $digit,
            ]);

        } catch (\Throwable $e) {
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

    private function toMinor($amount): int
    {
        // IQD has 0 decimals. Incoming may be string/decimal; normalize to int.
        return (int) round((float) $amount);
    }

    private function sendBillToPhenix(PhenixApiService $phenix, $order, $cartItems): bool
    {
        try {
            $now = Carbon::now('Asia/Baghdad');

            // totals stored on order
            $shippingIQD     = (int) ($order->shipping_amount ?? 0);
            $itemsPlusFeeIQD = (int) ($order->total_amount_iqd ?? 0); // items + payment fees (your DB rule)

            // subtotal of items after discounts (from cartItems final_price * qty)
            $itemsSubtotalIQD = (int) $cartItems->sum(function ($item) {
                $final = (float) ($item->final_price ?? 0);
                $qty   = (int) ($item->quantity ?? 1);
                return (int) round($final) * $qty;
            });

            $paymentFeesIQD = max(0, $itemsPlusFeeIQD - $itemsSubtotalIQD);

            /**
             * ✅ Group cart items by system_id (from product_variations.phenix_system_id)
             * Keys may come as strings from Collection, so we normalize later.
             */
            $groups = $cartItems->groupBy(function ($item) {
                return (int) data_get($item, 'product.variation.phenix_system_id', 0);
            });

            // invalid items (system_id missing)
            $invalidItems = $groups->get(0) ?? collect();
            if ($invalidItems->count() > 0) {
                Log::warning("Checkout: items missing phenix_system_id (skipped)", [
                    'order_id' => $order->id,
                    'items' => $invalidItems->map(fn($i) => [
                        'product_id' => $i->product_id ?? null,
                        'sku' => data_get($i, 'product.variation.sku'),
                    ])->values()->all(),
                ]);
            }

            // keep only valid system groups
            $groups = $groups->reject(fn($items, $sysId) => (int) $sysId <= 0);

            if ($groups->isEmpty()) {
                Log::warning("Checkout: no items with phenix_system_id, nothing sent to Phenix", [
                    'order_id' => $order->id,
                ]);
                return false;
            }

            // ✅ Load systems (id => system) including billtype/warehouseid
            $systemIds = $groups->keys()->map(fn($x) => (int) $x)->values()->all();

            $systems = PhenixSystem::query()
                ->whereIn('id', $systemIds)
                ->where('is_active', true)
                ->get(['id', 'name', 'code', 'billtype', 'warehouseid'])
                ->keyBy('id');

            /**
             * ✅ Remove groups whose system is missing/inactive
             * (otherwise allocation ratios break)
             */
            $groups = $groups->filter(function ($items, $sysId) use ($systems, $order) {
                $sysId = (int) $sysId;
                if (!$systems->has($sysId)) {
                    Log::error("Checkout: PhenixSystem not found/active, skipping system group", [
                        'order_id'   => $order->id,
                        'system_id'  => $sysId,
                        'item_count' => $items->count(),
                    ]);
                    return false;
                }
                return true;
            });

            if ($groups->isEmpty()) {
                Log::warning("Checkout: all system groups invalid/inactive, nothing sent", [
                    'order_id' => $order->id,
                ]);
                return false;
            }

            // ✅ Calculate each group subtotal (to allocate shipping/fees proportionally)
            $groupSubtotals = [];
            $totalAllGroups = 0;

            foreach ($groups as $sysId => $items) {
                $subtotal = (int) $items->sum(function ($item) {
                    $final = (float) ($item->final_price ?? 0);
                    $qty   = (int) ($item->quantity ?? 1);
                    return (int) round($final) * $qty;
                });

                $groupSubtotals[(int) $sysId] = $subtotal;
                $totalAllGroups += $subtotal;
            }

            // edge case: all are zero
            if ($totalAllGroups <= 0) {
                $totalAllGroups = 1;
            }

            // ✅ Allocate shipping + fees (rounding-safe)
            $allocShipping = [];
            $allocFees     = [];

            $remainingShipping = $shippingIQD;
            $remainingFees     = $paymentFeesIQD;

            $sysKeys = array_keys($groupSubtotals);
            $lastSys = (int) $sysKeys[array_key_last($sysKeys)];

            foreach ($groupSubtotals as $sysId => $subtotal) {
                $sysId = (int) $sysId;

                if ($sysId === $lastSys) {
                    // last system gets remainder
                    $allocShipping[$sysId] = $remainingShipping;
                    $allocFees[$sysId]     = $remainingFees;
                    break;
                }

                $ratio = $subtotal / $totalAllGroups;

                $s = (int) floor($shippingIQD * $ratio);
                $f = (int) floor($paymentFeesIQD * $ratio);

                $allocShipping[$sysId] = $s;
                $allocFees[$sysId]     = $f;

                $remainingShipping -= $s;
                $remainingFees     -= $f;
            }

            $allOk = true;

            // ✅ Send a bill per system
            foreach ($groups as $sysId => $items) {
                $sysId = (int) $sysId;

                /** @var PhenixSystem $system */
                $system = $systems->get($sysId);

                // ✅ Build details, but skip invalid item mappings safely
                $details = [];
                $skipped = [];

                foreach ($items as $item) {
                    $variation   = data_get($item, 'product.variation');

                    $unitId     = (int) data_get($variation, 'unit_id', 0);
                    $materialId = (int) data_get($variation, 'material_id', 0);

                    if ($unitId <= 0 || $materialId <= 0) {
                        $skipped[] = [
                            'product_id'  => $item->product_id ?? null,
                            'sku'         => data_get($variation, 'sku'),
                            'material_id' => $materialId,
                            'unit_id'     => $unitId,
                        ];
                        continue;
                    }

                    $baseMinor  = (int) round((float) ($item->base_price ?? data_get($variation, 'price', 0)));
                    $finalMinor = (int) round((float) ($item->final_price ?? $baseMinor));

                    $qty = (int) ($item->quantity ?? 1);
                    $discountMinor = max(0, $baseMinor - $finalMinor);

                    $details[] = [
                        "unitid"        => $unitId,
                        "itemprice"     => $baseMinor,                 // original price
                        "itemid"        => $materialId,
                        "discountvalue" => $discountMinor * $qty,      // discount amount
                        "quantity"      => $qty,
                        "vatvalue"      => 0,
                    ];
                }

                if (!empty($skipped)) {
                    Log::warning("Checkout: skipped items missing material_id/unit_id for system bill", [
                        'order_id'  => $order->id,
                        'system_id' => $sysId,
                        'system'    => $system->code ?? null,
                        'skipped'   => $skipped,
                    ]);
                }

                // If no valid details for this system, skip bill
                if (empty($details)) {
                    Log::warning("Checkout: no valid bill lines for system, bill not sent", [
                        'order_id'  => $order->id,
                        'system_id' => $sysId,
                        'system'    => $system->code ?? null,
                    ]);
                    $allOk = false;
                    continue;
                }

                // per-system note
                $note = trim(($order->first_name ?? '') . ' ' . ($order->last_name ?? '') . ' - ' . ($order->phone_number ?? ''))
                    . ' | Order: ' . ($order->tracking_number ?? $order->id)
                    . ' | System: ' . $system->name . ' (' . $system->code . ')'
                    . ' | Shipping: ' . number_format($allocShipping[$sysId] ?? 0) . ' IQD'
                    . ' | Payment Fees: ' . number_format($allocFees[$sysId] ?? 0) . ' IQD';

                // unique receipt id per system (avoid duplicates inside Phenix)
                $receiptId = (string) ($order->tracking_number ?? $order->id) . '-' . strtoupper((string) $system->code);

                // ✅ System-configured values (fallbacks just in case)
                $billType     = (int) ($system->billtype ?? 136);
                $warehouseId  = (int) ($system->warehouseid ?? 11);

                $payload = [
                    "_parameters" => [
                        [
                            "billdata" => [
                                "customerManipulateid" => 0,
                                "discountamount"       => 0,
                                "salesmanid"           => 0,
                                "receiptid"            => $receiptId,
                                "CheckDataValidation"  => 0,

                                "dateMonth"            => (int) $now->month,
                                "dateMinute"           => (int) $now->minute,
                                "dateYear"             => (int) $now->year,
                                "dateHour"             => (int) $now->hour,
                                "dateDay"              => (int) $now->day,

                                "iscash"               => 0,
                                "customerid"           => 0,
                                "billtype"             => $billType,
                                "currencyid"           => 2,
                                "note"                 => $note,
                                "warehouseid"          => $warehouseId,
                            ],
                            "billdetaildata" => $details,
                        ]
                    ]
                ];

                $res = $phenix->putBill($system->code, $payload);

                Log::info("Phenix Bill PUT success (system split)", [
                    'order_id'   => $order->id,
                    'tracking'   => $order->tracking_number,
                    'system_id'  => $sysId,
                    'system'     => $system->code,
                    'receipt'    => $receiptId,
                    'billtype'   => $billType,
                    'warehouse'  => $warehouseId,
                    'lines'      => count($details),
                    'response'   => $res,
                ]);
            }

            return $allOk;

        } catch (\Throwable $e) {
            Log::error("Phenix Bill PUT failed (system split): " . $e->getMessage(), [
                'order_id'  => $order->id ?? null,
                'tracking'  => $order->tracking_number ?? null,
            ]);
            return false;
        }
    }

}