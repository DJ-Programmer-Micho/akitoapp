<div class="container top">
    <div class="heading my-5">
        <h2 class="title text-center">{{__($title)}}</h2><!-- End .title -->
    </div><!-- End .heading -->

    <div class="tab-content tab-content-carousel just-action-icons-sm">
        <div class="row featured-products-loader">
            @for ($i = 0; $i < 4; $i++)
            <div class="col-3">
                <div class="product product-2">
                    <div class="card" aria-hidden="true">
                        <img src="https://placehold.co/600x400?text=Loading..." class="card-img-top" alt="...">
                        <div class="card-body">
                          <h5 class="card-title placeholder-glow">
                            <span class="placeholder col-6"></span>
                          </h5>
                          <p class="card-text placeholder-glow">
                            <span class="placeholder col-7"></span>
                            <span class="placeholder col-4"></span>
                            <span class="placeholder col-4"></span>
                            <span class="placeholder col-6"></span>
                            <span class="placeholder col-8"></span>
                          </p>
                          <a href="#" tabindex="-1" class="btn btn-primary disabled placeholder col-6"></a>
                        </div>
                      </div>
                </div>
            </div>
            @endfor
        </div>
            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": false,
                    "rtl": {{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'true' : 'false' }},
                    "lazyLoad": true,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":2
                        },
                        "600": {
                            "items":4
                        },
                        "992": {
                            "items":5
                        },
                        "1200": {
                            "items":6
                        }
                    }
                }'>
                @foreach ($productsData as $product)
                    <div class="product product-7 text-center">
                        <figure class="product-media">
                            <div class="label-wrapper" style="">
                                @if ($product->tags)
                                @foreach ($product->tags as $tag)
                                    <span class="col-3" style="background-color: #eb4034; color: #ffffff">{{$tag->tagTranslation->first()->name}}</span>
                                @endforeach
                                @endif
                            </div>
                            
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}">
                                <img src="{{ app('cloudfront') . ($product->variation->images->first()->image_path ?? 'path/to/default/image.jpg') }}" alt="{{ $product->productTranslation->first()->name ?? 'Product Image' }}" class="product-image">
                            </a>
            
                            @guest('customer')
                            <div class="heart-icon">
                                <button class="btn" href="#signin-modal" data-toggle="modal">
                                    <i class="fa-regular fa-heart"></i>
                                </button>
                            </div><!-- End .product-action-vertical -->
                            @endguest
                            @auth('customer')
                            @livewire('cart.wishlist-on-card-livewire', ['product_id' => $product->id])
                            @endauth
                            <div class="product-action">
                                @guest('customer')
                                <button type="button" class="btn-product btn-cart" href="#signin-modal" data-toggle="modal">
                                    <span>add to cart</span>
                                </button>
                                @endguest
                                @auth('customer')
                                <button type="button" class="btn-product btn-cart" onclick="addToCart({{ $product->id }})">
                                    <span>add to cart</span>
                                </button>
                                @endauth
                            </div><!-- End .product-action -->
                        </figure><!-- End .product-media -->
            
                        <div class="product-body">
                            <div class="product-cat">
                                <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $product->categories->first()->id]) }}">{{ $product->categories->first()->categoryTranslation->name ?? 'Category' }}</a>
                            </div><!-- End .product-cat -->
                            <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}">{{ $product->productTranslation->first()->name ?? 'Product Title' }}</a></h3><!-- End .product-title -->
                            <div class="bottom-class">
                                @if ($product->variation->discount)
                                <div class="product-price mt-1 fw-bold">
                                    $ {{$product->variation->discount}}
                                    <span class="mx-2" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                                        $ {{$product->variation->price}}
                                    </span>
                                </div><!-- End .product-price -->
                                @else
                                <div class="product-price mt-1 fw-bold">
                                    $ {{$product->variation->price}}
                                </div><!-- End .product-price -->
                                @endif
                                <div>
                                    <a type="button" class="btn btn-primary text-white">{{__('VIEW DETAILS')}}</a>
                                </div>
                            </div>
                        </div><!-- End .product-body -->
                    </div><!-- End .product -->
                @endforeach
            </div><!-- End .owl-carousel -->
    </div><!-- End .tab-content -->
</div><!-- End .container -->
<style>
    .product {
    display: flex;
    flex-direction: column;
    height: 100%; /* Ensure the product container takes full height if necessary */
}

.product-body {
    flex: 1; /* Allows the product-body to grow and take available space */
    display: flex;
    flex-direction: column;
    justify-content: space-between; /* Distributes space between content */
}

.bottom-class {
    margin-top: auto; /* Pushes the .bottom-class to the bottom of the container */
}

</style>