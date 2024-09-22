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

    protected $listeners = ['addToCart','cartUpdated' => 'loadCart'];

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
    
        $this->calculateTotals(); // Calculate totals after loading cart items
    }
    

    public function addToCart($productId)
    {
        try {
        $product = Product::findOrFail($productId);

        // Check if the product is already in the cart
        $cartItem = CartItem::where('customer_id', Auth::guard('customer')->id())
            ->where('product_id', $product->id)
            ->first();

        if ($cartItem) {
            // If it exists, increase the quantity
            $cartItem->quantity += 1;
            $cartItem->save();
        } else {
            // Add new item to the cart
            CartItem::create([
                'customer_id' => Auth::guard('customer')->id(),
                'product_id' => $product->id,
                'quantity' => 1,
            ]);
        }

        // Refresh the cart
        $this->loadCart();
        $this->emit('cartListUpdated'); // Emit event to update other components, if needed
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Item Added To Cart')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }

    }

    public function updateQuantity($cartItemId, $quantity)
    {
        try {

            $cartItem = CartItem::findOrFail($cartItemId);
            if ($quantity > 0) {
                $cartItem->update(['quantity' => $quantity]);
            } else {
                $this->removeFromCart($cartItemId);
            }
            
            $this->loadCart();
            $this->emit('cartListUpdated'); // Emit event to update other components, if needed
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Item Quantity Updated')]);
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
