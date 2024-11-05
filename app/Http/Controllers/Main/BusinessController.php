<?php

namespace App\Http\Controllers\Main;

use App\Models\User;
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
use App\Models\ProductVariation;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Mail\EmailInvoiceActionMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Events\EventCustomerOrderCheckout;
use Illuminate\Support\Facades\Notification;
use App\Events\EventNotifyCustomerOrderCheckout;
use App\Notifications\NotifyCustomerOrderCheckout;
use App\Notifications\Telegram\TeleNotifyCustomerOrder;

class BusinessController extends Controller
{
    // public function home() {
    //     $locale = app()->getLocale();  // Get the current locale
    
    //     // Fetch products for the first category
    //     $productsCat1 = Cache::remember("active_products_category_1_$locale", 60, function () use ($locale) {
    //         return Product::with([
    //             'productTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'variation.colors',
    //             'variation.sizes',
    //             'variation.materials',
    //             'variation.capacities',
    //             'variation.images',
    //             'brand.brandTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'categories.categoryTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'tags.tagTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             }
    //         ])
    //         ->where('status', 1)
    //         ->whereHas('categories', function ($query) {
    //             $query->where('categories.id', 2);
    //         })
    //         ->get();
    //     });
    
    //     // $productsCat1Title = $productsCat1->first()->categories->first()->categoryTranslation->title ?? __("messages.category_1_title");
    //     $productsCat1Title = "Coffee Makers";
    
    //     // Fetch products for the second category
    //     $productsCat2 = Cache::remember("active_products_category_2_$locale", 60, function () use ($locale) {
    //         return Product::with([
    //             'productTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'variation.colors',
    //             'variation.sizes',
    //             'variation.materials',
    //             'variation.capacities',
    //             'variation.images',
    //             'brand.brandTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'categories.categoryTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'tags.tagTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             }
    //         ])
    //         ->where('status', 1)
    //         ->whereHas('categories', function ($query) {
    //             $query->where('categories.id', 3);
    //         })
    //         ->get();
    //     });
    
    //     // $productsCat2Title = $productsCat2->first()->categories->first()->categoryTranslation->title ?? __("messages.category_2_title");
    //     $productsCat2Title = "Coffee Beans";
    
    //     // Fetch products for the third category
    //     $productsCat3 = Cache::remember("active_products_category_3_$locale", 60, function () use ($locale) {
    //         return Product::with([
    //             'productTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'variation.colors',
    //             'variation.sizes',
    //             'variation.materials',
    //             'variation.capacities',
    //             'variation.images',
    //             'brand.brandTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'categories.categoryTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             },
    //             'tags.tagTranslation' => function ($query) use ($locale) {
    //                 $query->where('locale', $locale);
    //             }
    //         ])
    //         ->where('status', 1)
    //         ->whereHas('categories', function ($query) {
    //             $query->where('categories.id', 2);
    //         })
    //         ->get();
    //     });
    
    //     // $productsCat3Title = $productsCat3->first()->categories->first()->categoryTranslation->title ?? __("messages.category_3_title");
    //     $productsCat3Title = "Syrup";

    //     $categoiresData = Category::where('status', 1)->with(['categoryTranslation' => function ($query) {
    //         $query->where('locale', app()->getLocale());
    //     }])
    //     ->get();
        
    //     $sliders = [];
    //     $sliders = [
    //         app('cloudfront') . 'web-setting/sliders/slide1.jpg',
    //         app('cloudfront') . 'web-setting/sliders/slide2.jpg',
    //         app('cloudfront') . 'web-setting/sliders/slide3.jpg',
    //     ];
        

    //     $imagesBanner = [];
    //     $imagesBanner = [
    //         app('cloudfront') . 'web-setting/banners/banner7.png',
    //         app('cloudfront') . 'web-setting/banners/banner8.png',
    //         app('cloudfront') . 'web-setting/banners/banner9.png',
    //     ];

    //     $featured = $this->fetchProducts('featured', 'Featured');
    //     $on_sale = $this->fetchProducts('on_sale', 'On Sale');

    //     return view('mains.pages.home-page-one', [
    //         'productsCat1' => $productsCat1,
    //         'productsCat1Title' => $productsCat1Title,
    //         'productsCat2' => $productsCat2,
    //         'productsCat2Title' => $productsCat2Title,
    //         'productsCat3' => $productsCat3,
    //         'productsCat3Title' => $productsCat3Title,
    //         'categoiresData' => $categoiresData,
    //         'imageBanner' => $imagesBanner,
    //         'featured_products' => $featured,
    //         'on_sale_products' => $on_sale,
    //         'sliders' => $sliders,
    //     ]);
    // }

    // private function fetchProducts($type, $title)
    // {
    //     $locale = app()->getLocale();
    //     $products = Product::with([
    //         'productTranslation' => function ($query) use ($locale) {
    //             $query->where('locale', $locale);
    //         },
    //         'variation.colors',
    //         'variation.sizes',
    //         'variation.materials',
    //         'variation.capacities',
    //         'variation.images',
    //         'brand.brandTranslation' => function ($query) use ($locale) {
    //             $query->where('locale', $locale);
    //         },
    //         'categories.categoryTranslation' => function ($query) use ($locale) {
    //             $query->where('locale', $locale);
    //         },
    //         'tags.tagTranslation' => function ($query) use ($locale) {
    //             $query->where('locale', $locale);
    //         }
    //     ])
    //     ->where('status', 1)
    //     ->whereHas('variation', function ($query) use ($type) {
    //         $query->where($type, 1);
    //     })
    //     ->whereHas('brand', function ($query) {
    //         $query->where('status', 1);
    //     })
    //     ->whereHas('categories', function ($query) {
    //         $query->where('status', 1);
    //     })
    //     ->get();

    //     return [
    //         'products' => $products,
    //         'title' => $title,
    //     ];
    // }
    
    public function home() {
        $locale = app()->getLocale(); // Get the current locale
        
        // Fetch products by category dynamically
        // $productsCat1 = $this->fetchProductsByCategory(2, "Coffee Makers", $locale);
        // $productsCat2 = $this->fetchProductsByCategory(3, "Coffee Beans", $locale);
        // $productsCat3 = $this->fetchProductsByCategory(4, "Syrup", $locale); // Assuming category ID 4 for Syrup

        $settings = WebSetting::find(1);

        $bannerImages = json_decode($settings->banner_images, true);

        // Initialize an array to store product data by category
        $categoryProducts = [];
        foreach ($bannerImages as $banner) {
            $categoryId = $banner['category_id'];
            $categoryProducts[] = $this->fetchProductsByCategory($categoryId, "", $locale);
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

        return view('mains.pages.home-page-one', [
            // 'productsCat1' => $productsCat1['products'],
            // 'productsCat1Title' => $productsCat1['title'],
            // 'productsCat2' => $productsCat2['products'],
            // 'productsCat2Title' => $productsCat2['title'],
            // 'productsCat3' => $productsCat3['products'],
            // 'productsCat3Title' => $productsCat3['title'],
            'categoryProducts' => $categoryProducts,
            'categoriesData' => $categoriesData,
            'imageBanner' => $bannerImages,
            'featured_products' => $featured,
            'on_sale_products' => $on_sale,
            'sliders' => $sortedSliders,
        ]);
    }

    private function fetchProductsByCategory($categoryId, $defaultTitle, $locale)
    {
        $customerId = auth('customer')->user()->id ?? null; // Assuming customer is logged in
        return Cache::remember("active_products_category_{$categoryId}_$locale", 60, function () use ($categoryId, $locale, $defaultTitle, $customerId) {
            $products = Product::with($this->productRelationships($locale))
                ->where('status', 1)
                ->whereHas('categories', function ($query) use ($categoryId) {
                    $query->where('categories.id', $categoryId);
                })
                ->get()
                ->map(function ($product) use ($customerId) {
                    $finalPrices = $this->calculateFinalPrice($product, $customerId);
                    $product->base_price = $finalPrices['base_price'];
                    $product->discount_price = $finalPrices['discount_price'];
                    $product->customer_discount_price = $finalPrices['customer_discount_price'];
                    return $product;
                });
    
            // Check if any product exists before trying to access categories
            $firstProduct = $products->first();
            $categoryTitle = $firstProduct && $firstProduct->categories->first() 
                ? $firstProduct->categories->first()->categoryTranslation->title 
                : $defaultTitle;
    
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

    // private function calculateFinalPrice($product, $customerId)
    // {
    //     $basePrice = $product->variation->discount ?? $product->variation->price; // Use original price
    //     $discountPrice = $product->variation->discount; // Use discounted price
    //     $customerDiscountPrice = null; // Default for customer discount
    
    //     // Check for applicable discounts
    //     if ($customerId) {
    //         $discountRule = DiscountRule::where('customer_id', $customerId)
    //             ->where(function ($query) use ($product) {
    //                 $query->where('product_id', $product->id)
    //                     ->orWhere('category_id', $product->categories->first()->id)
    //                     ->orWhere('brand_id', $product->brand_id);
    //             })
    //             ->first();
    
    //         // Apply discount rule if found
    //         if ($discountRule) {
    //             $discountPercentage = $discountRule->discount_percentage;
    //             $customerDiscountPrice = $basePrice - ($basePrice * ($discountPercentage / 100));
    //         }
    //     }
    
    //     return [
    //         'base_price' => $basePrice,
    //         'discount_price' => $discountPrice,
    //         'customer_discount_price' => $customerDiscountPrice,
    //     ];
    // }
    
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

    // private function getSliderImages()
    // {
    //     return [
    //         app('cloudfront') . 'web-setting/sliders/slide1.jpg',
    //         app('cloudfront') . 'web-setting/sliders/slide2.jpg',
    //         app('cloudfront') . 'web-setting/sliders/slide3.jpg',
    //     ];
    // }

    // private function getBannerImages()
    // {
    //     return [
    //         app('cloudfront') . 'web-setting/banners/banner7.png',
    //         app('cloudfront') . 'web-setting/banners/banner8.png',
    //         app('cloudfront') . 'web-setting/banners/banner9.png',
    //     ];
    // }

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
        // Get all active filters from the request
        $brandIds = $request->query('brands', []);
        $categoryIds = $request->query('categories', []);
        $subCategoryIds = $request->query('subcategories', []);
        $sizeIds = $request->query('sizes', []);
        $colorIds = $request->query('colors', []);
        $capacityIds = $request->query('capacities', []);
        $materialIds = $request->query('materials', []);
        $minPrice = floatval($request->query('min_price', 0));
        $maxPrice = floatval($request->query('max_price', 1000));
        $sortBy = $request->query('sortby', 'priority');
        $grid = $request->query('grid', 4);
    
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
                    $query->where(function ($query) {
                        $query->where('priority', 0)->orWhere('is_primary', 1);
                    });
                },
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
        $productQuery->when($brandIds, fn($query, $brandIds) => $query->whereIn('products.brand_id', $brandIds))
            ->when($categoryIds, fn($query, $categoryIds) => $query->whereHas('categories', fn($q) => $q->whereIn('category_id', $categoryIds)))
            ->when($subCategoryIds, fn($query, $subCategoryIds) => $query->whereHas('subCategories', fn($q) => $q->whereIn('sub_category_id', $subCategoryIds)))
            ->when($sizeIds, fn($query, $sizeIds) => $query->whereHas('variation.sizes', fn($q) => $q->whereIn('variation_size_id', $sizeIds)))
            ->when($colorIds, fn($query, $colorIds) => $query->whereHas('variation.colors', fn($q) => $q->whereIn('variation_color_id', $colorIds)))
            ->when($capacityIds, fn($query, $capacityIds) => $query->whereHas('variation.capacities', fn($q) => $q->whereIn('variation_capacity_id', $capacityIds)))
            ->when($materialIds, fn($query, $materialIds) => $query->whereHas('variation.materials', fn($q) => $q->whereIn('variation_material_id', $materialIds)))
            ->when([$minPrice, $maxPrice], fn($query) => $query->whereBetween('product_variations.price', [$minPrice, $maxPrice]));
    
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
                    return $query->orderBy('products.priority', 'asc');
            }
        });
    
        // Get the filtered products with pagination
        $products = $productQuery->paginate(1);
    
        // Loop through each product and calculate the final price with discounts
        $products->getCollection()->transform(function ($product) use ($request) {
            $discountDetails = $this->calculateFinalPrice($product, $request->user('customer')->id ?? null);
            $product->base_price = $discountDetails['base_price'];
            $product->discount_price = $discountDetails['discount_price'];
            $product->customer_discount_price = $discountDetails['customer_discount_price'];
            $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
            return $product;
        });

        // Query for Brands
        $brands = Brand::where('status', 1)
            ->whereHas('product', fn($q) => $q->where('is_spare_part', 0))
            ->with('brandtranslation')
            ->get();
        
        // Query for Categories
        $categories = Category::where('status', 1)
            ->whereHas('product', fn($q) => $q->where('is_spare_part', 0))
            ->with('categoryTranslation')
            ->get();
        
        // Query for SubCategories
        $subCategories = SubCategory::where('status', 1)
            ->whereHas('product', fn($q) => $q->where('is_spare_part', 0))
            ->with('subCategoryTranslation')
            ->get();
        
        // Query for Colors with Variations relationship
        $colors = VariationColor::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 0))
            ->get();
        
        // Query for Sizes
        $sizes = VariationSize::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 0))
            ->with('variationSizeTranslation')
            ->get();
        
        // Query for Capacities
        $capacities = VariationCapacity::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 0))
            ->with('variationCapacityTranslation')
            ->get();
        
        // Query for Materials
        $materials = VariationMaterial::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 0))
            ->with('variationMaterialTranslation')
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
        $minPrice = floatval($request->query('min_price', 0));
        $maxPrice = floatval($request->query('max_price', 1000));
        $sortBy = $request->query('sortby', 'priority');
        $grid = $request->query('grid', 4);
    
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
                    $query->where(function ($query) {
                        $query->where('priority', 0)->orWhere('is_primary', 1);
                    });
                },
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
        $productQuery->when($brandIds, fn($query, $brandIds) => $query->whereIn('products.brand_id', $brandIds))
            ->when($categoryIds, fn($query, $categoryIds) => $query->whereHas('categories', fn($q) => $q->whereIn('category_id', $categoryIds)))
            ->when($subCategoryIds, fn($query, $subCategoryIds) => $query->whereHas('subCategories', fn($q) => $q->whereIn('sub_category_id', $subCategoryIds)))
            ->when($sizeIds, fn($query, $sizeIds) => $query->whereHas('variation.sizes', fn($q) => $q->whereIn('variation_size_id', $sizeIds)))
            ->when($colorIds, fn($query, $colorIds) => $query->whereHas('variation.colors', fn($q) => $q->whereIn('variation_color_id', $colorIds)))
            ->when($capacityIds, fn($query, $capacityIds) => $query->whereHas('variation.capacities', fn($q) => $q->whereIn('variation_capacity_id', $capacityIds)))
            ->when($materialIds, fn($query, $materialIds) => $query->whereHas('variation.materials', fn($q) => $q->whereIn('variation_material_id', $materialIds)))
            ->when([$minPrice, $maxPrice], fn($query) => $query->whereBetween('product_variations.price', [$minPrice, $maxPrice]));
    
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
                    return $query->orderBy('products.priority', 'asc');
            }
        });
    
        // Get the filtered products with pagination
        $products = $productQuery->paginate(12);
    
        // Loop through each product and calculate the final price with discounts
        $products->getCollection()->transform(function ($product) use ($request) {
            $discountDetails = $this->calculateFinalPrice($product, $request->user('customer')->id ?? null);
            $product->base_price = $discountDetails['base_price'];
            $product->discount_price = $discountDetails['discount_price'];
            $product->customer_discount_price = $discountDetails['customer_discount_price'];
            $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
            return $product;
        });


        // Query for Brands
        $brands = Brand::where('status', 1)
            ->whereHas('product', fn($q) => $q->where('is_spare_part', 1))
            ->with('brandtranslation')
            ->get();
        
        // Query for Categories
        $categories = Category::where('status', 1)
            ->whereHas('product', fn($q) => $q->where('is_spare_part', 1))
            ->with('categoryTranslation')
            ->get();
        
        // Query for SubCategories
        $subCategories = SubCategory::where('status', 1)
            ->whereHas('product', fn($q) => $q->where('is_spare_part', 1))
            ->with('subCategoryTranslation')
            ->get();
        
        // Query for Colors with Variations relationship
        $colors = VariationColor::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 1))
            ->get();
        
        // Query for Sizes
        $sizes = VariationSize::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 1))
            ->with('variationSizeTranslation')
            ->get();
        
        // Query for Capacities
        $capacities = VariationCapacity::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 1))
            ->with('variationCapacityTranslation')
            ->get();
        
        // Query for Materials
        $materials = VariationMaterial::where('status', 1)
            ->whereHas('productVariations.product', fn($q) => $q->where('is_spare_part', 1))
            ->with('variationMaterialTranslation')
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
        $products = $productQuery->paginate(3);

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

    public function checkoutChecker($locale, $digit, $nvxf, Request $request){
        if(Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();
            if($customer->status == 1 && $customer->id == $nvxf){
                if($digit == 0) {
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
                        // $cartItems = CartItem::with('product','product.variation','product.productTranslation')->where('customer_id',$customer->id)->get();
                        $random_number = Str::random(6);
                        $customerId = $customer->id;
                        $cartItems = CartItem::with('product', 'product.variation', 'product.productTranslation')
                        ->where('customer_id', $customer->id)
                        ->get()
                        ->transform(function ($item) use ($customerId) {
                            $product = $item->product;

                            // Calculate the discount for the product
                            $discountDetails = $this->calculateFinalPrice($product, $customerId);

                            // Assign calculated discount details to the product
                            $product->base_price = $discountDetails['base_price'];
                            $product->discount_price = $discountDetails['discount_price'];
                            $product->customer_discount_price = $discountDetails['customer_discount_price'];
                            $product->total_discount_percentage = $discountDetails['total_discount_percentage'];

                            // Set the product price in the cart item based on customer-specific discount or general discount
                            $finalPrice = $product->customer_discount_price ?? $product->discount_price ?? $product->base_price;
                            $item->final_price = $finalPrice;

                            return $item;
                        });

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

                            // $pPrice = $item->product->variation->discount ? $item->product->variation->discount : $item->product->variation->price;
                            $pPrice = $item->final_price;
                            
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

                        try{
                            Notification::route('toTelegram', null)
                            ->notify(new TeleNotifyCustomerOrder(
                                $order->id,
                                $order->tracking_number,
                                $customerP->first_name .' '. $customerP->last_name,
                                $customerP->phone_number,
                                $cartItems,
                                $validatedData['shipping_amount'],
                                $validatedData['total_amount'],
                            ));
                            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Notification Send Successfully')]);
                        }  catch (\Exception $e) {
                            
                        }

                        DB::commit();
                        CartItem::where('customer_id', $customer->id)->delete();
                        $sum = 0;
                        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->where('tracking_number',$random_number)->first();
                        // Return view with data
                        foreach($order->orderItems as $item) {
                            $sum = $sum + $item->total;
                        }
                        // Mail::to($customer->email)->send(new EmailInvoiceActionMail($order, $sum));
                        return redirect()->route('business.checkout.success',['locale' => app()->getLocale()]);
                        // return 'Cash On Delivery';
                    } catch (\Exception $e) {
                        DB::rollBack();
                        dd($e);
                        return redirect()->route('business.checkout.faild',['locale' => app()->getLocale()]);
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

    public function checkSuccess(){
        return view('mains.components.livewire.aftercheckout.check-success', [
            
        ]);
    }
    public function checkFaild(){
        return view('mains.components.livewire.aftercheckout.check-failed', [
            
        ]);
    }
}