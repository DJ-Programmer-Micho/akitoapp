<div class="container featured">
    <ul class="nav nav-pills nav-border-anim nav-big justify-content-center mb-3" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" id="products-featured-link" data-toggle="tab" href="#products-featured-tab" role="tab" aria-controls="products-featured-tab" aria-selected="true">Featured</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" id="products-sale-link" data-toggle="tab" href="#products-sale-tab" role="tab" aria-controls="products-sale-tab" aria-selected="false">On Sale</a>
        </li>
        {{-- <li class="nav-item">
            <a class="nav-link" id="products-top-link" data-toggle="tab" href="#products-top-tab" role="tab" aria-controls="products-top-tab" aria-selected="false">Top Rated</a>
        </li> --}}
    </ul>

    <div class="tab-content tab-content-carousel">
        <div class="tab-pane p-0 fade show active" id="products-featured-tab" role="tabpanel" aria-labelledby="products-featured-link">
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
            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow featured-products-content" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": true,
                    "rtl": {{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'true' : 'false' }},
                    "lazyLoad": true,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":2
                        },
                        "600": {
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
                @foreach ($featured_products as $item)
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}">
                            <img src="{{app('cloudfront').$item->variation->images[0]->image_path ?? "sdf"}}" alt="{{$item->productTranslation->name[0]}}" class="product-image">
                        </a>

                        <div class="product-action-vertical">
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}" class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                        </div><!-- End .product-action -->

                        <div class="product-action product-action-dark">
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}" class="btn-product btn-cart" title="Add to cart"><span>add to cart</span></a>
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></a>
                        </div><!-- End .product-action -->
                    </figure><!-- End .product-media -->

                    <div class="product-body">
                        <div class="product-cat">
                            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $item->categories[0]->id]) }}">{{$item->categories[0]->categoryTranslation->name}}</a>
                        </div><!-- End .product-cat -->
                        <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}">{{$item->productTranslation->name}}</a></h3><!-- End .product-title -->
                        @if ($item->variation->discount)
                            <div class="product-price">
                                $ {{$item->variation->discount}}
                                <span class="mx-2" style="text-decoration: line-through; color: #999; font-size: 16px">
                                    $ {{$item->variation->price}}
                                </span>
                            </div><!-- End .product-price -->
                        @else
                            <div class="product-price">
                                $ {{$item->variation->price}}
                            </div><!-- End .product-price -->
                        @endif
                        {{-- <div class="ratings-container">
                            <div class="ratings">
                                <div class="ratings-val" style="width: 60%;"></div><!-- End .ratings-val -->
                            </div><!-- End .ratings -->
                            <span class="ratings-text">( 2 Reviews )</span>
                        </div><!-- End .rating-container --> --}}
                    </div><!-- End .product-body -->
                </div><!-- End .product -->
                @endforeach
            </div><!-- End .owl-carousel -->
        </div><!-- .End .tab-pane -->
        <div class="tab-pane p-0 fade" id="products-sale-tab" role="tabpanel" aria-labelledby="products-sale-link">
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
            <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow featured-products-content" data-toggle="owl" 
                data-owl-options='{
                    "nav": true, 
                    "dots": true,
                    "rtl": {{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'true' : 'false' }},
                    "lazyLoad": true,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "0": {
                            "items":2
                        },
                        "600": {
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
                @foreach ($on_sale as $item)
                <div class="product product-2">
                    <figure class="product-media">
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}">
                            <img src="{{app('cloudfront').$item->variation->images[0]->image_path ?? "sdf"}}" alt="{{$item->productTranslation->name[0]}}" class="product-image">
                        </a>

                        <div class="product-action-vertical">
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}" class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                        </div><!-- End .product-action -->

                        <div class="product-action product-action-dark">
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}" class="btn-product btn-cart" title="Add to cart"><span>add to cart</span></a>
                            <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></a>
                        </div><!-- End .product-action -->
                    </figure><!-- End .product-media -->

                    <div class="product-body">
                        <div class="product-cat">
                            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $item->categories[0]->id]) }}">{{$item->categories[0]->categoryTranslation->name}}</a>
                        </div><!-- End .product-cat -->
                        <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $item->productTranslation->slug])}}">{{$item->productTranslation->name}}</a></h3><!-- End .product-title -->
                        @if ($item->variation->discount)
                            <div class="product-price">
                                $ {{$item->variation->discount}}
                                <span class="mx-2" style="text-decoration: line-through; color: #999; font-size: 16px">
                                    $ {{$item->variation->price}}
                                </span>
                            </div><!-- End .product-price -->
                        @else
                            <div class="product-price">
                                $ {{$item->variation->price}}
                            </div><!-- End .product-price -->
                        @endif
                        {{-- <div class="ratings-container">
                            <div class="ratings">
                                <div class="ratings-val" style="width: 60%;"></div><!-- End .ratings-val -->
                            </div><!-- End .ratings -->
                            <span class="ratings-text">( 2 Reviews )</span>
                        </div><!-- End .rating-container --> --}}
                    </div><!-- End .product-body -->
                </div><!-- End .product -->
                @endforeach
            </div><!-- End .owl-carousel -->
        </div><!-- .End .tab-pane -->
    </div><!-- End .tab-content -->
</div><!-- End .container -->