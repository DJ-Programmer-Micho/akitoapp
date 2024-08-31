<div class="container top">
    <div class="heading heading-flex mb-3">
        <div class="heading-left">
            <h2 class="title">{{__($title)}}</h2><!-- End .title -->
        </div><!-- End .heading-left -->
    </div><!-- End .heading -->

    <div class="tab-content tab-content-carousel just-action-icons-sm">
            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": false,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":2
                        },
                        "480": {
                            "items":2
                        },
                        "768": {
                            "items":3
                        },
                        "992": {
                            "items":4
                        },
                        "1200": {
                            "items":5
                        }
                    }
                }'>
                @foreach ($productsData as $product)
                    <div class="product product-7 text-center">
                        <figure class="product-media">
                            <div class="label-wrapper">
                                @if ($product->tags)
                                @foreach ($product->tags as $tag)
                                    <span class="product-label col-3" style="background-color: #ef837b; color: #ffffff">{{$tag->tagTranslation->name}}</span>
                                @endforeach
                                @endif
                            </div>
                            
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}">
                                <img src="{{ app('cloudfront') . ($product->variation->images->first()->image_path ?? 'path/to/default/image.jpg') }}" alt="{{ $product->productTranslation->name ?? 'Product Image' }}" class="product-image">
                            </a>
            
                            <div class="product-action-vertical">
                                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product-icon btn-wishlist btn-expandable"><span>Add to wishlist</span></a>
                                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product-icon btn-quickview" title="Quick view"><span>Quick view</span></a>
                                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product-icon btn-compare" title="Compare"><span>Compare</span></a>
                            </div><!-- End .product-action-vertical -->
            
                            <div class="product-action">
                                <a href="#" class="btn-product btn-cart"><span>Add to cart</span></a>
                            </div><!-- End .product-action -->
                        </figure><!-- End .product-media -->
            
                        <div class="product-body">
                            <div class="product-cat">
                                <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $product->categories->first()->id]) }}">{{ $product->categories->first()->categoryTranslation->name ?? 'Category' }}</a>
                            </div><!-- End .product-cat -->
                            <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}">{{ $product->productTranslation->name ?? 'Product Title' }}</a></h3><!-- End .product-title -->
                            <div class="product-price mt-5 fw-bold">
                                $ {{ $product->variation->price ?? '0.00' }}
                            </div><!-- End .product-price -->
                            <div>
                                <a type="button" class="btn btn-primary text-white">{{__('VIEW DETAILS')}}</a>
                            </div>
                        </div><!-- End .product-body -->
                    </div><!-- End .product -->
            @endforeach
            </div><!-- End .owl-carousel -->
    </div><!-- End .tab-content -->
</div><!-- End .container -->