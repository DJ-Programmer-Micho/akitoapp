<div class="products mb-3">
    <div class="row justify-content-center">
        @foreach ($products as $product)
        <div class="col-6 col-md-4 col-lg-4 col-xl-3">
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
                        <img src="{{app('cloudfront').$product->variation->images[0]->image_path ?? "sdf"}}" alt="{{$product->productTranslation->name[0]}}" class="product-image">
                    </a>

                    <div class="product-action-vertical">
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product-icon btn-wishlist btn-expandable"><span>add to wishlist</span></a>
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product-icon btn-quickview" title="Quick view"><span>Quick view</span></a>
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product-icon btn-compare" title="Compare"><span>Compare</span></a>
                    </div><!-- End .product-action-vertical -->

                    <div class="product-action">
                        <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product btn-cart"><span>add to cart</span></a>
                    </div><!-- End .product-action -->
                </figure><!-- End .product-media -->

                <div class="product-body">
                    <div class="product-cat">
                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $product->categories[0]->id]) }}">{{$product->categories[0]->categoryTranslation->name}}</a>
                    </div><!-- End .product-cat -->
                    <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}">{{$product->productTranslation->name}}</a></h3><!-- End .product-title -->
                    <div class="product-price">
                        $ {{$product->variation->price}}
                    </div><!-- End .product-price -->

                    @if ($product->variation->images->count() > 1)
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
                    @endif
                </div><!-- End .product-body -->
            </div><!-- End .product -->
        </div><!-- End .col-sm-6 col-lg-4 col-xl-3 -->
        @endforeach
    </div><!-- End .row -->
</div><!-- End .products -->