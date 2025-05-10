<div class="products mb-3">
    <div class="row justify-content-center">
        @foreach ($products as $product)
        <div class="col-6 col-md-4 col-lg-4 mb-2">
            <div class="product product-7 text-center">
                <figure class="product-media">
                    <div class="label-wrapper" style="">
                        @if ($product->tags)
                        @foreach ($product->tags as $tag)
                            <span class="col-3" style="background-color: #eb4034; color: #ffffff">{{$tag->tagTranslation->name}}</span>
                        @endforeach
                        @endif
                    </div>
                    <a href="{{ route('business.productDetail', ['locale' => 'en','slug' => $product->productTranslation->first()->slug])}}">
                        <img loading="lazy" src="{{app('cloudfront').$product->variation->images[0]->image_path ?? "sdf"}}" alt="{{$product->productTranslation->first()->name[0]}}" class="product-image">
                    </a>

                    <div class="heart-icon">
                        <i class="fa-regular fa-heart"></i>
                        {{-- <i class="fa-solid fa-heart text-danger"></i> --}}
                    </div><!-- End .product-action-vertical -->
                    <div class="product-action">
                        @guest('customer')
                        <button href="#signin-modal" data-toggle="modal">
                            <span>{{ __('Add to Cart') }}</span>
                        </button>
                        @endguest
                        @auth('customer')
                        <button type="button" class="btn btn-product btn-cart" onclick="addToCart({{ $product->id }})">
                            <span>{{ __('Add to Cart') }}</span>
                        </button>
                        @endauth
                    </div><!-- End .product-action -->
                </figure><!-- End .product-media -->

                <div class="product-body">
                    <div class="product-cat text-center">
                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $product->categories[0]->id]) }}">{{$product->categories[0]->categoryTranslation->name}}</a>
                    </div><!-- End .product-cat -->
                    <h3 class="product-title text-center text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}">{{$product->productTranslation->first()->name}}</a></h3><!-- End .product-title -->
                    <div class="bottom-class">
                        @if ($product->discount_price == $product->customer_discount_price && $product->customer_discount_price != $product->base_price)
                        <div class="product-price mt-1 fw-bold">
                            <span class="mx-2 w-100 price flip-symbol justify-content-center" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                                <span class="amount">{{ number_format($product->base_price, 0) }}</span>
                                <span class="currency">{{ __('IQD') }}</span>
                            </span>
                            <span class="price flip-symbol justify-content-center">
                                <span class="amount">{{ number_format($product->discount_price, 0) }}</span>
                                <span class="currency">{{ __('IQD') }}</span>
                            </span>
                        </div><!-- End .product-price -->
                        @elseif ($product->discount_price != $product->customer_discount_price && $product->customer_discount_price != $product->base_price)
                        <div class="product-price mt-1 fw-bold">
                            <span class="mx-2 w-100 price flip-symbol justify-content-center" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                                <span class="amount">{{ number_format($product->base_price, 0) }}</span>
                                <span class="currency">{{ __('IQD') }}</span>
                            </span>
                            <span class="text-gold price flip-symbol justify-content-center" data-toggle="tooltip" data-placement="top" title="{{__('Only For You')}}">
                                {{-- <i class="fa-solid fa-star fa-beat-fade"></i> --}}
                                <span class="amount">{{ number_format($product->customer_discount_price, 0) }}</span>
                                <span class="currency">{{ __('IQD') }}</span>
                                {{-- <i class="fa-solid fa-star fa-beat-fade"></i> --}}
                            </span>
                        </div><!-- End .product-price -->
                    @else
                        <div class="product-price mt-1 fw-bold price flip-symbol justify-content-center">
                            <span class="amount">{{ number_format($product->base_price, 0) }}</span>
                            <span class="currency">{{ __('IQD') }}</span>
                        </div><!-- End .product-price -->
                    @endif
                        <div>
                            @if ($product->variation->stock)
                            <a type="button" href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->first()->slug])}}" class="btn btn-primary text-white">{{__('VIEW DETAILS')}}</a>
                            @else
                            <button type="button" class="btn btn-primary text-white" disabled>
                                <i class="fa-solid fa-cubes mr-1"></i> {{__('Out Of Stock')}}
                            </button>
                            @endif
                        </div>
                    </div>
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

.product-7 {
    style="-webkit-box-shadow: 0px 6px 24px -5px rgba(0,0,0,0.20);
    -moz-box-shadow: 0px 6px 24px -5px rgba(0,0,0,0.20);
    box-shadow: 0px 6px 24px -5px rgba(0,0,0,0.20);"
}
</style>