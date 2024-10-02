<div class="dropdown cart-dropdown">
    <a href="#" class="dropdown-toggle" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" data-display="static">
        <lord-icon
            src="https://cdn.lordicon.com/odavpkmb.json"
            trigger="loop"
            delay="2000"
            colors="primary:#3080e8,secondary:#000000"
            style="width:40px;height:40px">
        </lord-icon>
        <span class="cart-count">{{$totalQuantity}}</span>
    </a>
    <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-cart-products">
            @forelse($cartItems as $item)
            <div class="product d-flex align-items-center justify-content-between p-2" style="flex-direction:unset;">
                <div class="product-cart-details flex-grow-1 d-flex flex-column">
                    <h4 class="product-title">
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}">
                            {{ $item['product']['product_translation'][0]['name'] }}
                        </a>
                    </h4>
                    <span class="cart-product-info">
                        <span class="cart-product-qty">{{ $item['quantity'] }}</span>
                        x ${{ !empty($item['product']['variation']['on_sale']) ? $item['product']['variation']['discount'] : $item['product']['variation']['price'] }}
                    </span>
                    <div class="quantity-action d-flex align-items-center">
                        @if($item['quantity'] > 1)
                            <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] - 1 }})" class="btn btn-outline-secondary btn-sm" style="min-width: 35px; padding: 5px; border-radius: 40px 0 0 40px">
                                <i class="fa-solid fa-minus"></i>
                            </button>
                        @else
                            <button wire:click="removeFromCart({{ $item['id'] }})" class="btn btn-outline-danger btn-sm" style="min-width: 35px; padding: 5px; border-radius: 40px 0 0 40px">
                                <i class="fa-regular fa-trash-can"></i>
                            </button>
                        @endif
                        {{-- <span class="mx-2">{{ $item['quantity'] }}</span> --}}
                        <button wire:click="updateQuantity({{ $item['id'] }}, {{ $item['quantity'] + 1 }})" class="btn btn-outline-secondary btn-sm" style="min-width: 35px; padding: 5px; border-radius: 0 40px 40px 0">
                            <i class="fa-solid fa-plus"></i>
                        </button>
                    </div>
                </div>

                <figure class="product-image-container ml-2">
                    <a href="product.html" class="product-image">
                        <img src="{{ app('cloudfront') . $item['product']['variation']['images'][0]['image_path'] ?? '' }}" alt="product" style="width: 80px; height: 80px; object-fit: cover;">
                    </a>
                </figure>
    

            </div><!-- End .product -->
            @empty
            {{__('No Items Are Added')}}

            @endforelse
        </div><!-- End .cart-product -->
        @if ($cartItems)
        <div class="dropdown-cart-total mt-2">
            <span>Total</span>
            <span class="cart-total-price">${{ $totalPrice }}</span>
        </div><!-- End .dropdown-cart-total -->
    
        <div class="dropdown-cart-action mt-2">
            <a href="{{ route('business.viewcart', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}" class="btn btn-outline-primary-2">View Cart</a>
            <a href="{{ route('business.checkout', ['locale' => app()->getLocale(),'slug' => $item['product']['product_translation'][0]['slug']])}}" class="btn btn-outline-primary-2">Checkout</a>
            {{-- <button wire:click="checkout" class="btn btn-outline-primary-2"><span>Checkout</span><i class="icon-long-arrow-right"></i></button> --}}
        </div><!-- End .dropdown-cart-action -->
        @endif
    </div><!-- End .dropdown-menu -->
    


<script>
    function addToCart(productId) {
        Livewire.emit('addToCart', productId);
    }
</script>

</div><!-- End .cart-dropdown -->
