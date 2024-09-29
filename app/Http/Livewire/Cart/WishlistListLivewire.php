<?php

namespace App\Http\Livewire\Cart;

use Livewire\Component;
use App\Models\CartItem;
use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;

class WishlistListLivewire extends Component
{
    public $wishlistItems = [];

    protected $listeners = ['refreshWishlist', 'wishlistUpdated'];

    public function mount()
    {
        $this->loadWishlist();
    }

    public function loadWishlist()
    {
        $locale = app()->getLocale();
        $customerId = Auth::guard('customer')->id();

        $this->wishlistItems = WishlistItem::where('customer_id', $customerId)
            ->with(['product.productTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }, 'product.variation', 'product.variation.images'])
            ->get()
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

    public function singleAddWishlist($productId)
    {
        try {
            $customerId = Auth::guard('customer')->id();
    
            // Check if the item already exists in the cart
            $existsInCart = CartItem::where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->exists();
    
            if (!$existsInCart) {
                // Add the item to the cart
                CartItem::create([
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                    'quantity' => 1, // Assuming you want to add one item to the cart
                ]);
    
                // Remove the item from the wishlist

    
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item added to cart successfully and removed from wishlist.')]);
                $this->emit('cartUpdated'); // Emit event to refresh the cart view if needed
                $this->emit('wishlistUpdated'); // Emit event to refresh the wishlist view if needed
                $this->emit('refreshWish');
            } else {
                $wishlistItem = WishlistItem::where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->first();

                if ($wishlistItem) {
                    $wishlistItem->delete(); // Delete the wishlist item
                }
                $this->emit('wishlistUpdated');
                $this->emit('cartUpdated');
                $this->emit('refreshWish');
                $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('Item already in cart.')]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something went wrong.')]);
        }
    }
    

    public function fromWishlistToCart()
    {
        try {
            $customerId = Auth::guard('customer')->id();
            
            foreach ($this->wishlistItems as $item) {
                // Check if the item already exists in the cart to avoid duplicates
                $existsInCart = CartItem::where('customer_id', $customerId)
                    ->where('product_id', $item['product_id'])
                    ->exists();

                if (!$existsInCart) {
                    CartItem::create([
                        'customer_id' => $customerId,
                        'product_id' => $item['product_id'],
                        'quantity' => 1,
                    ]);
                }
            }
            WishlistItem::where('customer_id', $customerId)->delete();
            $this->emit('wishlistUpdated');
            $this->emit('cartUpdated');
            $this->emit('refreshWish');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Items Added To Cart')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }
    }

    public function render()
    {
        return view('mains.components.livewire.cart.wishlist-table', [
            'wishlistItems' => $this->wishlistItems,
        ]);
    }
}
