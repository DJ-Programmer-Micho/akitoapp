<div class="products mb-3">
    @foreach ($products as $product)
    <div class="product product-list">
        <div class="row">
        <div class="col-6 col-lg-3">
            <figure class="product-media">
                <div class="label-wrapper" style="">
                    @if ($product->tags)
                    @foreach ($product->tags as $tag)
                        <span class="col-3" style="background-color: #eb4034; color: #ffffff">{{$tag->tagTranslation->name}}</span>
                    @endforeach
                    @endif
                </div>
                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}">
                    <img src="{{app('cloudfront').$product->variation->images[0]->image_path ?? "sdf"}}" alt="{{$product->productTranslation->name[0]}}" class="product-image">
                </a>
                <div class="heart-icon">
                    <i class="fa-regular fa-heart"></i>
                    {{-- <i class="fa-solid fa-heart text-danger"></i> --}}
                </div><!-- End .product-action-vertical -->
            </figure><!-- End .product-media -->
        </div><!-- End .col-sm-6 col-lg-3 -->

        <div class="col-6 col-lg-3 order-lg-last">
            <div class="product-list-action">
                
                {{-- <div class="ratings-container">
                    <div class="ratings">
                        <div class="ratings-val" style="width: 20%;"></div><!-- End .ratings-val -->
                    </div><!-- End .ratings -->
                    <span class="ratings-text">( 2 Reviews )</span>
                </div><!-- End .rating-container --> --}}

                {{-- <div class="product-action">
                    <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product btn-quickview" title="Quick view"><span>quick view</span></a>
                    <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product btn-compare" title="Compare"><span>compare</span></a>
                </div><!-- End .product-action --> --}}

                <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn btn-primary text-white"><span>add to cart</span></a>
                <div class="my-3"></div>
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
                <div class="product-nav product-nav-thumbs">
                    @foreach ($product->variation->colors->take(3) as $index => $color)
                    <a href="#" class="active">
                        <div style="height: 100%; width: 100%; background-color: {{$color->code}};"></div>
                    </a>
                    @endforeach
                </div><!-- End .product-nav -->
                @endif
            </div><!-- End .product-list-action -->
        </div><!-- End .col-sm-6 col-lg-3 -->

        <div class="col-lg-6">
            <div class="product-body product-action-inner">
                {{-- <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}" class="btn-product btn-wishlist" title="Add to wishlist"><span>add to wishlist</span></a> --}}
                <div class="product-cat">
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $product->categories[0]->id]) }}">{{$product->categories[0]->categoryTranslation->name}}</a>
                </div><!-- End .product-cat -->
                <h3 class="product-title text-clamp-2"><a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $product->productTranslation->slug])}}">{{$product->productTranslation->name}}</a></h3><!-- End .product-title -->

                <div class="product-content">
                    <p class="text-clamp-2">{{$product->productTranslation->description}}</p>
                </div><!-- End .product-content -->
                
                @if ($product->variation->discount)
                <div class="product-price">
                    $ {{$product->variation->discount}}
                    <span class="mx-2" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                        $ {{$product->variation->price}}
                    </span>
                </div><!-- End .product-price -->
                @else
                    <div class="product-price">
                        $ {{$product->variation->price}}
                    </div><!-- End .product-price -->
                @endif

            </div><!-- End .product-body -->
        </div><!-- End .col-lg-6 -->

    </div><!-- End .row -->
    </div><!-- End .row -->
        @endforeach
</div><!-- End .products -->