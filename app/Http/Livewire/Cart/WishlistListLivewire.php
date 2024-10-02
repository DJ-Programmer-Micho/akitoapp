<?php

namespace App\Http\Livewire\Cart;

use App\Models\Product;
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
    
            // Check if the item is already in the cart
            $existsInCart = CartItem::where('customer_id', $customerId)
                ->where('product_id', $productId)
                ->exists();
    
            // Get the product to check stock availability
            $product = Product::find($productId);
    
            // Check if the product is in stock
            if ($product && $product->variation->stock > 0) {
                if (!$existsInCart) {
                    // Add the item to the cart
                    CartItem::create([
                        'customer_id' => $customerId,
                        'product_id' => $productId,
                        'quantity' => 1, // Assuming you want to add one item to the cart
                    ]);
    
                    // Remove the item from the wishlist
                    $wishlistItem = WishlistItem::where('customer_id', $customerId)
                        ->where('product_id', $productId)
                        ->first();
    
                    if ($wishlistItem) {
                        $wishlistItem->delete(); // Delete the wishlist item
                    }
    
                    $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Item added to cart successfully and removed from wishlist.')]);
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('Item already in cart.')]);
                }
            } else {
                // Product is out of stock
                $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Item is out of stock and remains in your wishlist.')]);
            }
    
            $this->emit('cartUpdated'); // Emit event to refresh the cart view if needed
            $this->emit('wishlistUpdated'); // Emit event to refresh the wishlist view if needed
            $this->emit('refreshWish');
    
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something went wrong.')]);
        }
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
                if ($product && $product->stock > 0) {
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
        return view('mains.components.livewire.cart.wishlist-table', [
            'wishlistItems' => $this->wishlistItems,
        ]);
    }
}
