{{-- resources/views/mains/components/livewire/cart/wishlist-oncard-one.blade.php --}}
{{-- WishlistOnCardLivewire --}}
<div class="heart-icon">
    <button class="btn" wire:click="addToWishlist({{ $product_id }})">
        @if (in_array($product_id, $wishlistItemsVisibility))
            <i class="fa-solid fa-heart text-danger"></i> <!-- Selected for wishlist -->
        @else
            <i class="fa-regular fa-heart"></i> <!-- Not in wishlist -->
        @endif
    </button>
</div><!-- End .product-action-vertical -->
