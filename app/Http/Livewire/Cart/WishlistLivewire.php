<?php

namespace App\Http\Livewire\Cart;

use App\Models\Product;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\DiscountRule;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;

class WishlistLivewire extends Component
{
    public $wishlistItems = [];
    public $exchange_rate;
    protected $listeners = ['refreshWishlist', 'wishlistUpdated'];

    public function mount()
    {
        $this->exchange_rate = config('currency.exchange_rate');
        $this->loadWishlist();
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

    public function loadWishlist()
    {
        $locale = app()->getLocale();
        $customerId = Auth::guard('customer')->id();

        $this->wishlistItems = WishlistItem::where('customer_id', $customerId)
            ->with(['product.productTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }, 'product.variation', 'product.variation.images'=> function ($imageQuery) {
                // Filter images to include only those with priority 0 or is_primary 1
                $imageQuery->where(function ($imageQuery) {
                    $imageQuery->where('priority', 0)
                               ->orWhere('is_primary', 1);
                });
            }])
            ->get()
            ->transform(function ($wishlistItem) use ($customerId) {
                $product = $wishlistItem->product;
        
                // Calculate the discount for the product
                $discountDetails = $this->calculateFinalPrice($product, $customerId);
        
                // Assign calculated discount details to the product
                $product->base_price = $discountDetails['base_price'];
                $product->discount_price = $discountDetails['discount_price'];
                $product->customer_discount_price = $discountDetails['customer_discount_price'];
                $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
        
                return $wishlistItem;
            })
            ->toArray();
    }

    public function removeFromWishlist($productId)
    {
        try {

            $wishlistItem = WishlistItem::where('customer_id', Auth::guard('customer')->id())
                ->where('product_id', $productId)
                ->first();
                
            if ($wishlistItem) {
                $wishlistItem->delete();
            }

            $this->loadWishlist();
            $this->emit('wishlistUpdated');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Item Removed From Wish List')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }
    }

    public function wishlistUpdated()
    {
        $this->loadWishlist();
    }

    public function fromWishlistToCart()
    {
        try {
            $customerId = Auth::guard('customer')->id();
    
            $addedToCartProductIds = [];
            // Initialize an array to track out-of-stock products that shouldn't be removed from the wishlist
            $outOfStockItems = [];
    
            foreach ($this->wishlistItems as $item) {

                // Check if the product is already in the cart
                $existsInCart = CartItem::where('customer_id', $customerId)
                    ->where('product_id', $item['product_id'])
                    ->exists();
    
                if ($existsInCart) {
                    continue; // Skip adding the product if it's already in the cart
                }
    
                $product = Product::with(['productTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                }])->find($item['product_id']);

                // Check if the product is available in stock
                if ($product && $product->order_limit > 0) {
                    // Add the product to the cart
                    CartItem::create([
                        'customer_id' => $customerId,
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ]);

                    // Track the added product ID
                    $addedToCartProductIds[] = $product->id;
                } else {
                    // Check if product translation exists before accessing it
                    if ($product) {
                        $outOfStockItems[] = $product->productTranslation->first()->name ?? __('Unknown Product');
                    } else {
                        $outOfStockItems[] = __('Unknown Product');
                    }

                    WishlistItem::where('customer_id', $customerId)
                    ->where('product_id', $product->id)
                    ->delete();
                }
            }
            // Remove the in-stock products from the wishlist (those that were added to the cart)
    
            // Emit events to refresh the wishlist and cart UI components
            $this->emit('wishlistUpdated');
            $this->emit('cartUpdated');
            $this->emit('refreshWish');
    
            // Check if there are any out-of-stock items
            if (!empty($outOfStockItems)) {
                $outOfStockMessage = __('Some items are out of stock and remain in your wishlist: ') . implode(', ', $outOfStockItems);
                $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => $outOfStockMessage]);
            } else {
                // Notify success if all items were successfully added to the cart
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Items successfully added to your cart')]);
            }
    
        } catch (\Exception $e) {
            // Handle exceptions and notify the user of an error
            dd($e);
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something went wrong')]);
        }
    }

    public function render()
    {
        return view('mains.components.livewire.cart.wishlist-one', [
            'wishlistItems' => $this->wishlistItems,
        ]);
    }
}
