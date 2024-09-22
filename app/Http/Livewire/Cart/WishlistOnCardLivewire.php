<?php

namespace App\Http\Livewire\Cart;

use App\Models\WishlistItem;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class WishlistOnCardLivewire extends Component
{
    public $product_id;
    public $wishlistItems = [];

    protected $listeners = ['refreshWishlist','refreshWish'];

    public function mount($product_id)
    {
        $this->product_id = $product_id;
        $this->loadWishlist();
    }

    public function addToWishlist($productId)
    {
        try {
            $customerId = Auth::guard('customer')->id();
            $wishlistItem = WishlistItem::where('customer_id', $customerId)
            ->where('product_id', $productId)
            ->first();
            
            if ($wishlistItem) {
                // If the item is already in the wishlist, remove it
                $wishlistItem->delete();
            } else {
                // If not, add it to the wishlist
                WishlistItem::create([
                    'customer_id' => $customerId,
                    'product_id' => $productId,
                ]);
            }
            
            // Refresh the wishlist items
            $this->loadWishlist();
            $this->emit('wishlistUpdated');
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Item Added To Wish List')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong')]);
        }
    }

    public function refreshWish(){
        $this->loadWishlist();
    }
    public function loadWishlist()
    {
        $this->wishlistItems = WishlistItem::where('customer_id', Auth::guard('customer')->id())
            ->pluck('product_id')
            ->toArray();
    }

    public function render()
    {
        return view('mains.components.livewire.cart.wishlist-on-card-one', [
            'wishlistItemsVisibility' => $this->wishlistItems,
        ]);
    }
}
