<div class="dropdown wishlist cart-dropdown">
    <a href="#" class="dropdown-toggle" role="button" data-toggle="wishlist" aria-haspopup="true" aria-expanded="false" data-display="static">
        <lord-icon
            src="https://cdn.lordicon.com/jjoolpwc.json"
            trigger="loop"
            delay="2000"
            colors="primary:#3080e8,secondary:#000000"
            style="width:40px;height:40px">
        </lord-icon>
        <span class="cart-count">{{ count($wishlistItems) }}</span>
    </a>
    <div class="wishlist dropdown-menu dropdown-menu-right">
        <div class="dropdown-cart-products">
            @forelse($wishlistItems as $item)
                <div class="product d-flex align-items-center justify-content-between p-2" style="flex-direction:unset;">
                    <div class="product-cart-details flex-grow-1 d-flex flex-column">
                        <h4 class="product-title">
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}">{{ $item['product']['product_translation'][0]['name'] }}</a>
                        </h4>
                    </div>
                    <figure class="product-image-container ml-2">
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}" class="product-image">
                            <img src="{{ app('cloudfront') . $item['product']['variation']['images'][0]['image_path'] ?? '' }}" alt="product" style="width: 80px; height: 80px; object-fit: cover;">
                        </a>
                    </figure>
                    <button type="button" class="btn btn-danger p-1" style="min-width: 20px; border-radius: 8px" wire:click="removeFromWishlist({{$item['product']['id']}})">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    
                </div><!-- End .product -->
            @empty
                {{__('No Items Are Added')}}
            @endforelse
        </div><!-- End .cart-product -->
        @if ($wishlistItems)
        <div class="dropdown-cart-action mt-2">
            <button wire:click="fromWishlistToCart" class="btn btn-outline-primary-2"><span>{{ __('Add Them To Cart') }}</span><i class="icon-long-arrow-right"></i></button>
        </div><!-- End .dropdown-cart-action -->
        @endif
    </div><!-- End .dropdown-menu -->
</div><!-- End .cart-dropdown -->