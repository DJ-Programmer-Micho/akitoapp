<div class="owl-carousel owl-simple carousel-equal-height carousel-with-shadow" data-toggle="owl" 
                        data-owl-options='{
                            "nav": false, 
                            "dots": true,
                            "margin": 20,
                            "loop": false,
                            "responsive": {
                                "0": {
                                    "items":1
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
                                    "items":4,
                                    "nav": true,
                                    "dots": false
                                }
                            }
                        }'>
                        @foreach ($recommends as $item)
                        <div class="product product-7">
                            <figure class="product-media">
                                <a href="product.html">
                                    <img src="{{app('cloudfront').$item->variation->images[0]->image_path ?? "sdf"}}" alt="{{$item->productTranslation->name[0]}}" class="product-image">
                                </a>

                                <div class="product-action-vertical">
                                    <a href="#" class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                                </div><!-- End .product-action -->

                                <div class="product-action product-action-dark">
                                    <a href="#" class="btn-product btn-cart" title="Add to cart"><span>add to cart</span></a>
                                    <a href="popup/quickView.html" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></a>
                                </div><!-- End .product-action -->
                            </figure><!-- End .product-media -->

                            <div class="product-body">
                                <div class="product-cat">
                                    <a href="#">{{$item->categories[0]->categoryTranslation->name}}</a>
                                </div><!-- End .product-cat -->
                                <h3 class="product-title"><a href="product.html">{{$item->productTranslation->name}}</a></h3><!-- End .product-title -->
                                <div class="product-price">
                                    {{$item->variation->price}}
                                </div><!-- End .product-price -->
                                <div class="ratings-container">
                                    <div class="ratings">
                                        <div class="ratings-val" style="width: 60%;"></div><!-- End .ratings-val -->
                                    </div><!-- End .ratings -->
                                    <span class="ratings-text">( 2 Reviews )</span>
                                </div><!-- End .rating-container -->

                                @if ($item->variation->images->count() > 1)
                                <div class="product-nav product-nav-thumbs">
                                    @foreach ($item->variation->images->take(3) as $index => $image)
                                    <a href="#" class="active">
                                        <img src="{{app('cloudfront').$image->image_path ?? "sdf"}}" alt="product desc">
                                    </a>
                                    @endforeach
                                </div><!-- End .product-nav -->
                                @endif

                                @if ($item->variation->colors->count() > 1)
                                <div class="product-nav product-nav-thumbs">
                                    @foreach ($item->variation->colors as $index => $color)
                                    <a href="#" class="active">
                                        <div style="height: 100%; width: 100%; background-color: {{$color->code}};"></div>
                                    </a>
                                    @endforeach
                                </div><!-- End .product-nav -->
                                @endif
                            </div><!-- End .product-body -->
                        </div><!-- End .product -->
                        @endforeach

                    </div><!-- End .owl-carousel -->