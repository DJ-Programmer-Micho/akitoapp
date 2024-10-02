<?php

namespace App\Http\Livewire\Cart;

use Livewire\Component;
use App\Models\Product;
use App\Models\CartItem;
use Illuminate\Support\Facades\Auth;

class CartLivewire extends Component
{
    public $cartItems = [];
    public $totalQuantity = 0;
    public $totalPrice = 0;

    protected $listeners = ['addToCart','addToCartDetail','cartUpdated' => 'loadCart'];

    public function mount()
    {
        $this->loadCart();
    }

    public function loadCart()
    {
        $locale = app()->getLocale(); // Get the current locale
    
        $this->cartItems = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->with(['product' => function ($query) use ($locale) {
                $query->with(['productTranslation' => function ($subQuery) use ($locale) {
                    // Fetch the translation for the current locale
                    $subQuery->where('locale', $locale);
                }, 'variation','variation.images']);
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
    
    public function addToCart($productId)
    {
        try {
            $product = Product::findOrFail($productId);
            $availableQty = $product->variation->stock ?? 0;
    
            // Check if the product is already in the cart
            $cartItem = CartItem::where('customer_id', Auth::guard('customer')->id())
                ->where('product_id', $product->id)
                ->first();
    
            if ($cartItem) {
                if ($cartItem->quantity < $availableQty) {
                    $cartItem->quantity += 1;
                    $cartItem->save();
                    $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item Added To Cart')]);
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Insufficient Stock')]);
                }
            } else {
                if ($availableQty > 0) {
                    CartItem::create([
                        'customer_id' => Auth::guard('customer')->id(),
                        'product_id' => $product->id,
                        'quantity' => 1,
                    ]);
                    $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item Added To Cart')]);
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Out Of Stock')]);
                }
            }
    
            // Refresh the cart only if an item was added or changed
            $this->loadCart();
            $this->emit('cartListUpdated');
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }
    }
    

    public function addToCartDetail($productId, $qty)
    {
        try {
            $product = Product::findOrFail($productId);
            $availableQty = $product->variation->stock ?? 0;
    
            // Check if the product is already in the cart
            $cartItem = CartItem::where('customer_id', Auth::guard('customer')->id())
                ->where('product_id', $product->id)
                ->first();
    
                if ($cartItem) {
                    if ($cartItem->quantity + $qty <= $availableQty) {
                        $cartItem->quantity += $qty;
                        $cartItem->save();
                        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item Added To Cart')]);
                    } else {
                        $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Insufficient Stock')]);
                    }
                } else {
                    if ($qty <= $availableQty) {
                        CartItem::create([
                            'customer_id' => Auth::guard('customer')->id(),
                            'product_id' => $product->id,
                            'quantity' => $qty,
                        ]);
                        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item Added To Cart')]);
                    } else {
                        $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Out Of Stock')]);
                    }
                }                
    
            // Refresh the cart
            $this->loadCart();
            $this->emit('cartListUpdated'); // Emit event to update other components, if needed
            // $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Item Added To Cart')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }
    }

    public function updateQuantity($cartItemId, $quantity)
    {
        try {
            $cartItem = CartItem::findOrFail($cartItemId);
            $availableQty = $cartItem->product->variation->stock ?? 0; // Fetch available stock
    
            if ($quantity > $availableQty) {
                $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Insufficient Stock')]);
            } elseif ($quantity > 0) {
                // Update quantity only if it doesn't exceed available stock
                $cartItem->update(['quantity' => $quantity]);
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item Quantity Updated')]);
            } else {
                // Remove item if quantity is set to 0 or lower
                $this->removeFromCart($cartItemId);
            }
    
            $this->loadCart(); // Reload cart after updating
            $this->emit('cartListUpdated'); // Emit event to update other components, if needed
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }
    }
    

    public function removeFromCart($cartItemId)
    {
        try {

            $cartItem = CartItem::findOrFail($cartItemId);
            $cartItem->delete();
            
            $this->loadCart();
            $this->emit('cartListUpdated'); // Emit event to update other components, if needed
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Item Removed To Cart')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }

    }

    public function calculateTotals()
    {
        $this->totalQuantity = array_sum(array_column($this->cartItems, 'quantity'));
        $this->totalPrice = array_reduce($this->cartItems, function ($carry, $item) {
            // Retrieve the current price from the product's variation
            $productPrice = $item['product']['variation']['price'] ?? 0;
            
            // Check if the product is on sale and adjust the price accordingly
            if (!empty($item['product']['variation']['on_sale'])) {
                $productPrice = $item['product']['variation']['discount'] ?? $productPrice; // Use the discount if available
            }
    
            return $carry + ($item['quantity'] * $productPrice);
        }, 0);
    }

    public function render()
    {
        return view('mains.components.livewire.cart.cart-one', [
            'cartItems' => $this->cartItems,
            'totalQuantity' => $this->totalQuantity,
            'totalPrice' => $this->totalPrice,
        ]);
    }
}
