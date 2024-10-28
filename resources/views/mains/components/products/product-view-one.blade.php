<div class="product-details-top">
    <style>
        .owl-carousel .item img {
            width: 100%; /* Ensure the image fits the container */
            height: auto; /* Keep aspect ratio */
            display: block;
            object-fit: cover; /* Scale images to fill container */
        }

        /* Hide carousel until fully initialized */
        .owl-carousel {
            opacity: 0;
            transition: opacity 0.3s ease; /* Smooth transition */
        }

        .owl-loaded {
            opacity: 1;
        }

        .hover-text:hover {
            color: whitel
        }
    </style>
    <div class="row">
        <div class="col-md-6">
            <div class="product-gallery">
                <!-- Main Image with Fancybox -->
                <div class="product-main-image mb-3 image-preview-slider">
                    <a id="main-image-fancybox" data-fancybox="gallery" href="{{ app('cloudfront').$productDetail->variation->images[0]->image_path ?? 'default.jpg' }}">
                        <img id="product-zoom" src="{{ app('cloudfront').$productDetail->variation->images[0]->image_path ?? 'default.jpg' }}" alt="product image">
                    </a>
                </div>
        
                {{-- <div class="product-image-gallery featured-products-loader">
                    Loading...
                </div>
                 --}}
                <!-- Thumbnails Carousel -->
                <div class="featured-products-content" style="display: none">
                    @if ($productDetail->variation->images->count() > 1)
                    <div class="owl-carousel owl-theme product-image-gallery" id="product-zoom-gallery">
                        @foreach ($productDetail->variation->images as $image)
                        <div class="item">
                            <a href="{{ app('cloudfront').$image->image_path ?? 'default.jpg' }}" data-fancybox="gallery" data-image="{{ app('cloudfront').$image->image_path ?? 'default.jpg' }}">
                                <img src="{{ app('cloudfront').$image->image_path ?? 'default.jpg' }}" alt="product side">
                            </a>
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
        
            </div>
        </div>

        <div class="col-md-6">
            <div class="product-details">
                <div class="nav-dir">
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $productDetail->brand->id]) }}">
                        <img src="{{ app('cloudfront').$productDetail->brand->image ?? 'default.jpg'}}" alt="{{$productDetail->brand->brandTranslation->name}}" width="80">
                    </a>
                </div>
                <h6 class="nav-dir">
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $productDetail->brand->id]) }}">
                        {{$productDetail->brand->brandTranslation->name}}
                    </a>
                    /
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $productDetail->categories[0]->id]) }}">
                        {{$productDetail->categories[0]->categoryTranslation->name}}
                    </a>
                </h6><!-- End .product-title -->
                <h1 class="product-title px-0"><b>{{$productDetail->productTranslation->name}}</b></h1><!-- End .product-title -->

                {{-- <div class="ratings-container">
                    <div class="ratings">
                        <div class="ratings-val" style="width: 80%;"></div><!-- End .ratings-val -->
                    </div><!-- End .ratings -->
                    <a class="ratings-text" href="#product-review-link" id="review-link">( 2 Reviews )</a>
                </div><!-- End .rating-container --> --}}
                @if ($productDetail->discount_price == $productDetail->customer_discount_price && $productDetail->customer_discount_price != $productDetail->base_price)
                    <div class="product-price mt-1 fw-bold">
                        <span class="mx-2 w-100" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                        $ {{ number_format($productDetail->base_price, 2) }}
                        </span>
                        <span>
                            $ {{ number_format($productDetail->discount_price, 2) }}
                        </span>
                    </div><!-- End .product-price -->
                @elseif ($productDetail->discount_price != $productDetail->customer_discount_price && $productDetail->customer_discount_price != $productDetail->base_price)
                    <div class="product-price mt-1 fw-bold">
                        <span class="mx-2 w-100" style="text-decoration: line-through; color: #cc0022; font-size: 16px">
                            $ {{ number_format($productDetail->base_price, 2) }}
                        </span>
                        <span class="text-gold" data-toggle="tooltip" data-placement="top" title="{{__('Only For You')}}">
                            {{-- <i class="fa-solid fa-star fa-beat-fade"></i> --}}$ {{ number_format($productDetail->customer_discount_price, 2) }}{{-- <i class="fa-solid fa-star fa-beat-fade"></i> --}}
                        </span>
                    </div><!-- End .product-price -->
                @else
                    <div class="product-price mt-1 fw-bold">
                        $ {{ number_format($productDetail->base_price, 2) }}
                    </div><!-- End .product-price -->
                @endif
            


                <div class="product-content">
                    <p style="font-size: 14pt">{{$productDetail->productTranslation->description}}</p>
                </div><!-- End .product-content -->

                @if ($productDetail->variation->colors->count() > 1)
                <div class="details-filter-row details-row-size nav-dir">
                    <label>{{__('Color:')}}</label>

                    <div class="product-nav product-nav-thumbs">
                        @foreach ($productDetail->variation->colors as $color)
                        <a href="#">
                            <div style="height: 100%; width: 100%; background-color: {{$color->code}};border: 1px solid black"></div>
                        </a>
                        @endforeach
                    </div><!-- End .product-nav -->
                </div><!-- End .details-filter-row -->
                @endif

                @if ($productDetail->variation->sizes->count() >= 1)
                <div class="details-filter-row details-row-size nav-dir">
                    <label for="size">{{__('Size:')}}</label>
                    <p class="mx-1"><b>{{$productDetail->variation->sizes->first()->variationSizeTranslation->name}}</b></p>
                    {{-- <div class="select-custom mx-1">
                        <select name="size" id="size" class="form-control">
                            <option value="#" selected="selected">{{__('Select a size')}}</option>
                            @foreach ($productDetail->variation->sizes as $size)
                            <option value="{{$size->id}}">{{$size->variationSizeTranslation->name}}</option>
                            @endforeach
                        </select>
                    </div><!-- End .select-custom --> --}}

                    {{-- <a href="#" class="size-guide"><i class="icon-th-list"></i>size guide</a> --}}
                </div><!-- End .details-filter-row -->
                @endif
                @if ($productDetail->variation->capacities->count() >= 1)
                <div class="details-filter-row details-row-size nav-dir">
                    <label for="size">{{__('Capacity:')}}</label>
                    <p class="mx-1"><b>{{$productDetail->variation->capacities->first()->variationCapacityTranslation->name}}</b></p>
                    {{-- <div class="select-custom mx-1">
                        <select name="capacity" id="capacity" class="form-control">
                            <option value="#" selected="selected">{{__('Select a Capacity')}}</option>
                            @foreach ($productDetail->variation->capacities as $capacity)
                            <option value="{{$capacity->id}}">{{$capacity->variationCapacityTranslation->name}}</option>
                            @endforeach
                        </select>
                    </div><!-- End .select-custom --> --}}
                    {{-- <a href="#" class="size-guide"><i class="icon-th-list"></i>size guide</a> --}}
                </div><!-- End .details-filter-row -->
                @endif
                @if ($productDetail->variation->stock)
                <div class="details-filter-row details-row-size nav-dir">
                    <label for="qty">{{__('Qty:')}}</label>
                    <div class="product-details-quantity mx-1">
                        <input type="number" id="qty" class="form-control" value="1" min="1" max="{{$productDetail->variation->order_limit}}" step="1" data-decimals="0" required>
                    </div><!-- End .product-details-quantity -->
                </div><!-- End .details-filter-row -->
                @endif
                <div class="product-details-action mx-1 nav-dir">
                    @if ($productDetail->variation->stock)
                    <button type="button" onclick="addToCartDetail({{ $productDetail->id }})" class="btn hover-text btn-product btn-cart">
                     {{__('add to cart')}}
                    </button>
                    @else
                    <button type="button" class="btn btn-primary text-white" disabled>
                        <i class="fa-solid fa-cubes mr-1"></i> {{__('Out Of Stock')}}
                    </button>
                    @endif
                    <div class="details-action-wrapper detail-icon">
                        @guest('customer')
                        <div class="heart-icon">
                            <button class="btn" href="#signin-modal" data-toggle="modal">
                                <i class="fa-regular fa-heart"></i>
                            </button>
                        </div><!-- End .product-action-vertical -->
                        @endguest
                        @auth('customer')
                        @livewire('cart.wishlist-on-card-livewire', ['product_id' => $productDetail->id])
                        @endauth
                        {{-- <a href="#" class="btn-product btn-wishlist" title="Wishlist"><span>Add to Wishlist</span></a> --}}


                        {{-- <a href="#" class="btn-product btn-compare" title="Compare"><span>Add to Compare</span></a> --}}
                    </div><!-- End .details-action-wrapper -->
                </div><!-- End .product-details-action -->

                <div class="product-details-footer">
                    @if ($productDetail->variation->materials->count() > 1)
                    <div class="product-cat">
                        <span>Materials:</span>
                        @foreach ($productDetail->variation->materials as $material)
                        <a href="#">{{$material->variationMaterialTranslation->name}}</a>,
                        @endforeach
                    </div><!-- End .product-cat -->
     
                    @endif
                    @if ($productDetail->variation->capacities->count() > 1)
                    <div class="product-cat">
                        <span>Capacities:</span>
                        @foreach ($productDetail->variation->capacities as $capacity)
                        <a href="#">{{$capacity->variationCapacityTranslation->name}}</a>,
                        @endforeach
                    </div><!-- End .product-cat -->
                    @endif


                    <div class="row w-100">
                        <div class="col-md-6 col-12">
                            <div class="icon-box icon-box-side nav-dir">
                                <span class="icon-box-icon text-dark">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/zzjjvkam.json"
                                        trigger="loop"
                                        state="loop-ride"
                                        colors="primary:#3080e8,secondary:#000000"
                                        style="width:50px;height:50px">
                                    </lord-icon>
                                </span>
                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">{{__('Fast shipping')}}</h3><!-- End .icon-box-title -->
                                    <p>{{__('Receive your order on time, every time')}}</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="icon-box icon-box-side nav-dir">
                                <span class="icon-box-icon text-dark">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/urswgamh.json"
                                        trigger="loop"
                                        state="loop-ride"
                                        colors="primary:#3080e8,secondary:#000000"
                                        style="width:50px;height:50px">
                                    </lord-icon>
                                </span>
                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">{{__('Always authentic')}}</h3><!-- End .icon-box-title -->
                                    <p>{{__('We only sell 100% authentic products')}}</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="icon-box icon-box-side nav-dir">
                                <span class="icon-box-icon text-dark">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/iztkybbu.json"
                                        trigger="loop"
                                        state="loop-ride"
                                        colors="primary:#3080e8,secondary:#000000"
                                        style="width:50px;height:50px">
                                    </lord-icon>
                                </span>
                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">{{__('Easy return')}}</h3><!-- End .icon-box-title -->
                                    <p>{{__('Return policy that lets you shop at ease')}}</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div>
                        <div class="col-md-6 col-12">
                            <div class="icon-box icon-box-side nav-dir">
                                <span class="icon-box-icon text-dark">
                                    <lord-icon
                                        src="https://cdn.lordicon.com/fgxwhgfp.json"
                                        trigger="loop"
                                        state="loop-ride"
                                        colors="primary:#3080e8,secondary:#000000"
                                        style="width:50px;height:50px">
                                    </lord-icon>
                                </span>
                                <div class="icon-box-content">
                                    <h3 class="icon-box-title">{{__('Secure shopping')}}</h3><!-- End .icon-box-title -->
                                    <p>{{__('Your data will always be protected')}}</p>
                                </div><!-- End .icon-box-content -->
                            </div><!-- End .icon-box -->
                        </div>
                    </div>

                </div><!-- End .product-details-footer -->
                <hr>
                <div class="social-icons social-icons-sm nav-dir">
                    <span class="social-label">{{__('Share:')}}</span>
                    <a href="#" class="social-icon" title="Facebook" target="_blank"><i class="icon-facebook-f"></i></a>
                    <a href="#" class="social-icon" title="Twitter" target="_blank"><i class="icon-twitter"></i></a>
                    <a href="#" class="social-icon" title="Instagram" target="_blank"><i class="icon-instagram"></i></a>
                    <a href="#" class="social-icon" title="Pinterest" target="_blank"><i class="icon-pinterest"></i></a>
                </div>
            </div><!-- End .product-details -->
        </div><!-- End .col-md-6 -->
    </div><!-- End .row -->
</div><!-- End .product-details-top -->

@push('productView')
<script>
$(document).ready(function(){
    // Initialize Owl Carousel
    $("#product-zoom-gallery").owlCarousel({
        nav: true,
        dots: false,
        margin: 10,
        items: 4,
        loop: false
    });

    // Change Main Image on Thumbnail Click
    $("#product-zoom-gallery .item a").on("click", function(event) {
        event.preventDefault();  // Prevent default link behavior
        var newImage = $(this).data("image");  // Get the image URL from data attribute
        $("#product-zoom").attr("src", newImage);  // Update the main image source
        $("#main-image-fancybox").attr("href", newImage);  // Update the Fancybox link href
    });

    // Initialize Fancybox for image zoom
    $('[data-fancybox="gallery"]').fancybox({
        loop: true,
        buttons: [
            'slideShow',
            'thumbs',
            'close'
        ]
    });
});

function addToCartDetail(productId) {
    const qty = document.getElementById('qty').value;
    Livewire.emit('addToCartDetail', productId, qty);
}
</script>
@endpush
