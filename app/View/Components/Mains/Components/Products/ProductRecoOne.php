<?php

namespace App\View\Components\Mains\Components\Products;

use Closure;
use App\Models\Product;
use App\Models\DiscountRule;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use App\Models\ProductRecommendation;

class ProductRecoOne extends Component
{
    public $recommends;
    public $locale;
    public $productId;
    public $titleName;

    public function __construct($locale, $id)
    {
        $this->locale = $locale;
        $this->productId = $id;

        // Fetch recommended product IDs based on the provided product ID
        $recommendedProductIds = ProductRecommendation::where('product_id', $id)
            ->pluck('recommended_product_id')
            ->toArray(); // Convert to array for empty check

            if(!empty($recommendedProductIds)) {
                $this->recommends = $this->getRecommendedProducts($recommendedProductIds);
                $this->titleName = "You May Also Like";
            } else {
                $this->recommends = $this->getRecommendedProductsNotDefined();
                $this->titleName = "Related Products";
            }
    }

    private function getRecommendedProducts(array $recommendedProductIds)
    {
        $recommendedProducts = Product::with([
            'productTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'variation.colors',
            'variation.sizes.variationSizeTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'variation.materials',
            'variation.capacities',
            'variation.images' => function ($query) {
                // Here you can filter the images based on your requirements
                $query->where(function ($query) {
                    $query->where('priority', 0)
                          ->orWhere('is_primary', 1);
                });
            },
            'brand.brandTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'categories.categoryTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'tags.tagTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'information.informationTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            }
        ])
        ->where('status', 1)
        ->whereIn('id', $recommendedProductIds)
        ->get();

        $recommendedProducts->transform(function ($product) {
            $customerId = request()->user('customer')->id ?? null;
            $discountDetails = $this->calculateFinalPrice($product, $customerId);
    
            // Assign calculated discount details to the product
            $product->base_price = $discountDetails['base_price'];
            $product->discount_price = $discountDetails['discount_price'];
            $product->customer_discount_price = $discountDetails['customer_discount_price'];
            $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
    
            return $product;
        });
        return $recommendedProducts;

    }

    private function getRecommendedProductsNotDefined()
    {
        // Load the current product along with its sub-categories
        $currentProduct = Product::with('subCategories')->find($this->productId);
        if (!$currentProduct) {
            // If the current product cannot be found, return an empty collection
            return collect();
        }

        // Get an array of the current product's sub-category IDs
        $subCategoryIds = $currentProduct->subCategories->pluck('id')->toArray();

        // Query for products that:
        // - Have status = 1
        // - Are not the current product itself
        // - Belong to at least one of the same sub-categories
        $recommendedProducts = Product::with([
            'productTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'variation.colors',
            'variation.sizes.variationSizeTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'variation.materials',
            'variation.capacities',
            'variation.images' => function ($query) {
                $query->where(function ($query) {
                    $query->where('priority', 0)
                        ->orWhere('is_primary', 1);
                });
            },
            'brand.brandTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'categories.categoryTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'tags.tagTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            },
            'information.informationTranslation' => function ($query) {
                $query->where('locale', $this->locale);
            }
        ])
        ->where('status', 1)
        ->where('id', '<>', $this->productId) // Exclude current product
        ->whereHas('subCategories', function ($query) use ($subCategoryIds) {
            $query->whereIn('sub_categories.id', $subCategoryIds);
        })
        ->limit(8)
        ->get();

        // Transform each product to add discount details
        $recommendedProducts->transform(function ($product) {
            $customerId = request()->user('customer')->id ?? null;
            $discountDetails = $this->calculateFinalPrice($product, $customerId);

            // Assign the calculated discount details to the product
            $product->base_price                = $discountDetails['base_price'];
            $product->discount_price            = $discountDetails['discount_price'];
            $product->customer_discount_price   = $discountDetails['customer_discount_price'];
            $product->total_discount_percentage = $discountDetails['total_discount_percentage'];

            return $product;
        });

        // Return the products (remove or comment out dd() for production)
        // dd($recommendedProducts);
        return $recommendedProducts;
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
    

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.products.product-reco-one', [
            'recommends' => $this->recommends,
            'title_name' => $this->titleName,
        ]);
    }
}
