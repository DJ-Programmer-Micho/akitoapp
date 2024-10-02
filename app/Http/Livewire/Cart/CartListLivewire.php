<?php

namespace App\Http\Livewire\Cart;

use Livewire\Component;
use App\Models\Product;
use App\Models\CartItem;
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

    public function loadCartList()
    {
        $locale = app()->getLocale(); // Get the current locale
    
        $this->cartListItems = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->with(['product' => function ($query) use ($locale) {
                $query->with(['productTranslation' => function ($subQuery) use ($locale) {
                    // Fetch the translation for the current locale
                    $subQuery->where('locale', $locale);
                }, 'variation','variation.images','categories','categories.categoryTranslation']);
            }])
            ->get()
            ->toArray();
    
        foreach ($this->cartItems as $index => $cartItem) {
            $availableQty = $cartItem['product']['variation']['stock'] ?? 0;
    
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

            $availableQty = $product->variation->stock ?? 0; // Check stock availability

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
            $availableQty = $cartItem->product->variation->stock ?? 0; // Check available stock

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
            // Retrieve the current price and original price from the product's variation
            $productPrice = $item['product']['variation']['price'] ?? 0;
            $originalPrice = $item['product']['variation']['original_price'] ?? $productPrice;
    
            // Check if the product is on sale and adjust the price accordingly
            if (!empty($item['product']['variation']['on_sale'])) {
                $discountPrice = $item['product']['variation']['discount'] ?? $productPrice; // Use the discount price if available
                $this->totalDiscount += ($originalPrice - $discountPrice) * $item['quantity']; // Add discount to total discount
                $productPrice = $discountPrice; // Use the discount price for total price calculation
            }
    
            // Add the product price multiplied by quantity to the total price
            $this->totalListPrice += $item['quantity'] * $productPrice;
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
