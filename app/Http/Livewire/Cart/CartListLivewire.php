<?php

namespace App\Http\Livewire\Cart;

use App\Models\Product;
use Livewire\Component;
use App\Models\CartItem;
use App\Models\DiscountRule;
use Illuminate\Support\Facades\Auth;

class CartListLivewire extends Component
{
    public $cartListItems = [];
    public $totalListQuantity = 0;
    public $totalListPrice = 0;
    public $totalDiscount = 0;

    protected $listeners = ['addToCartList','cartListUpdated' => 'loadCartList'];

    public function mount()
    {
        $this->loadCartList();
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

    
    public function loadCartList()
    {
        $locale = app()->getLocale(); // Get the current locale
        $customerId = Auth::guard('customer')->id();

        $this->cartListItems = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->with(['product' => function ($query) use ($locale) {
                $query->with(['productTranslation' => function ($subQuery) use ($locale) {
                    // Fetch the translation for the current locale
                    $subQuery->where('locale', $locale);
                }, 'variation', 
        'categories.categoryTranslation' => function ($categoryQuery) use ($locale) {
            // Optional: Filter category translations by locale if needed
            $categoryQuery->where('locale', $locale);
        },
        'variation.images' => function ($imageQuery) {
            // Filter images to include only those with priority 0 or is_primary 1
            $imageQuery->where(function ($query) {
                $query->where('priority', 0)
                      ->orWhere('is_primary', 1);
            });
        }]);
            }])
            ->get()
            ->transform(function ($cartListItems) use ($customerId) {
                $product = $cartListItems->product;
        
                // Calculate the discount for the product
                $discountDetails = $this->calculateFinalPrice($product, $customerId);
        
                // Assign calculated discount details to the product
                $product->base_price = $discountDetails['base_price'];
                $product->discount_price = $discountDetails['discount_price'];
                $product->customer_discount_price = $discountDetails['customer_discount_price'];
                $product->total_discount_percentage = $discountDetails['total_discount_percentage'];
        
                return $cartListItems;
            })
            ->toArray();
    
        foreach ($this->cartListItems as $index => $cartItem) {
            $availableQty = $cartItem['product']['variation']['order_limit'] ?? 0;
    
            if ($cartItem['quantity'] > $availableQty) {
                if ($availableQty > 0) {
                    // Reduce the quantity in the cart to match available stock
                    CartItem::where('id', $cartItem['id'])->update(['quantity' => $availableQty]);
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Item QTY Removed')]);
                } else {
                    // If the product is out of stock, remove it from the cart
                    CartItem::where('id', $cartItem['id'])->delete();
                    unset($this->cartItems[$index]);
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Item Removed From Cart')]);
                }
            }            
        }
        
        $this->calculateTotals(); // Calculate totals after loading cart items
    }
    

    public function addToCartList($productId)
    {
        try {
            $product = Product::findOrFail($productId);

            $availableQty = $product->variation->order_limit ?? 0; // Check stock availability

            // Check if the product is already in the cart
            $cartItem = CartItem::where('customer_id', Auth::guard('customer')->id())
                ->where('product_id', $product->id)
                ->first();

            if ($cartItem) {
                if ($cartItem->quantity + 1 > $availableQty) {
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Insufficient Stock')]);
                } else {
                    $cartItem->quantity += 1;
                    $cartItem->save();
                }
            } else {
                if (1 > $availableQty) {
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Insufficient Stock')]);
                } else {
                    CartItem::create([
                        'customer_id' => Auth::guard('customer')->id(),
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ]);
                }
            }

            // Refresh the cart
            $this->loadCartList();
            $this->emit('cartUpdated'); // Emit event to update other components, if needed
            // $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item Added To Cart')]);

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something Went Wrong')]);
        }
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        try {
            $cartItem = CartItem::findOrFail($cartItemId);
            $availableQty = $cartItem->product->variation->order_limit ?? 0; // Check available stock

            if ($quantity > $availableQty) {
                $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Insufficient Stock')]);
            } elseif ($quantity > 0) {
                $cartItem->update(['quantity' => $quantity]);
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item Quantity Updated')]);
            } else {
                $this->removeFromCartList($cartItemId);
            }

            $this->loadCartList();
            $this->emit('cartUpdated'); // Emit event to update other components, if needed
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something Went Wrong')]);
        }
    }

    public function removeFromCartList($cartItemId)
    {
        try {

            $cartItem = CartItem::findOrFail($cartItemId);
            $cartItem->delete();
            
            $this->loadCartList();
            $this->emit('cartUpdated'); // Emit event to update other components, if needed
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Item Removed To Cart')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }

    }

    // FOR THE DISCOUNT CALCULATIONS TOTAL
    public function calculateTotals()
    {
        $this->totalListQuantity = array_sum(array_column($this->cartListItems, 'quantity'));
        
        // Initialize total price and total discount
        $this->totalListPrice = 0;
        $this->totalDiscount = 0;
        
        // Loop through the cart items to calculate total price and discount
        foreach ($this->cartListItems as $item) {
            // Initialize variables for pricing
            $basePrice = $item['product']['variation']['price'] ?? 0;
            $discountPrice = $item['product']['variation']['discount'] ?? $basePrice;
            $customerDiscountPrice = $item['product']['customer_discount_price'] ?? null;
    
            // Determine the price to use based on priority
            $finalPrice = $customerDiscountPrice ?? $discountPrice;
    
            // Calculate total discount if applicable
            if ($customerDiscountPrice && $customerDiscountPrice < $basePrice) {
                $this->totalDiscount += ($basePrice - $customerDiscountPrice) * $item['quantity'];
            } elseif ($discountPrice < $basePrice) {
                $this->totalDiscount += ($basePrice - $discountPrice) * $item['quantity'];
            }
    
            // Add the product price multiplied by quantity to the total price
            $this->totalListPrice += $item['quantity'] * $finalPrice;
        }
    }
    public function render()
    {
        return view('mains.components.livewire.cart.cart-table', [
            'cartListItems' => $this->cartListItems,
            'totalListQuantity' => $this->totalListQuantity,
            'totalListPrice' => $this->totalListPrice,
            'totalDiscount' => $this->totalDiscount,
        ]);
    }
}
