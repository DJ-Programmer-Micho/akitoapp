<div class="products mb-3">
    <div class="row justify-content-center">
        @foreach ($products as $product)
        <div class="col-6 col-md-4 col-lg-4 col-xl-3 mb-2">
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
                        <img src="{{app('cloudfront').$product->variation->images[0]->image_path ?? "sdf"}}" alt="{{$product->productTranslation->first()->name[0]}}" class="product-image">
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
                        {{-- <i class="fa-solid fa-heart text-danger"></i> --}}
                    <div class="product-action">
                        @guest('customer')
                        <button href="#signin-modal" data-toggle="modal">
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
                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $product->categories[0]->id]) }}">{{$product->categories[0]->categoryTranslation->first()->name}}</a>
                    </div><!-- End .product-cat -->
                    <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}">{{$product->productTranslation->first()->name}}</a></h3><!-- End .product-title -->
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
                        
                    {{-- @if ($product->variation->images->count() > 1)
                    <div class="product-nav product-nav-thumbs">
                        @foreach ($product->variation->images->take(3) as $index => $image)
                        <a href="#" class="active">
                            <img src="{{app('cloudfront').$image->image_path ?? "sdf"}}" alt="product desc">
                        </a>
                        @endforeach
                    </div><!-- End .product-nav -->
                    @endif
                    
                    @if ($product->variation->colors->count() >= 1)
                    <div class="">

                        <div class="product-nav product-nav-thumbs bottom-0">
                            @foreach ($product->variation->colors as $index => $color)
                            <a href="#" class="active">
                                <div style="height: 100%; width: 100%; background-color: {{$color->code}};"></div>
                            </a>
                            @endforeach
                        </div><!-- End .product-nav -->
                    </div>
                    @endif --}}
                </div><!-- End .product-body -->
            </div><!-- End .product -->
        </div><!-- End .col-sm-6 col-lg-4 col-xl-3 -->
        @endforeach
    </div><!-- End .row -->
</div><!-- End .products -->
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