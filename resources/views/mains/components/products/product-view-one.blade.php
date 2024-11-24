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
                        <img src="{{ app('cloudfront').$productDetail->brand->image ?? 'default.jpg'}}" alt="{{$productDetail->brand->brandTranslation->name}}" width="120">
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
                    /
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $productDetail->categories[0]->id, 'subcategories[]' => $productDetail->subCategories[0]->id]) }}">
                        {{$productDetail->subCategories[0]->subCategoryTranslation->name}}
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
                    <p style="font-size: 12pt">{{$productDetail->productTranslation->description}}</p>
                </div><!-- End .product-content -->
                {{-- <div id="chart" class="p-0 m-0"></div> --}}

                @if ($productDetail->variation->colors->count() > 1)
                <div class="details-filter-row details-row-size nav-dir m-0">
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
                <div class="details-filter-row details-row-size nav-dir m-0">
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
                <div class="details-filter-row details-row-size nav-dir m-0">
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

                @if (count($productDetail->variation->intensity) >= 1 || $productDetail->variation->capacities->count() >= 1 || $productDetail->variation->sizes->count() >= 1)
                <hr>
                <div class="container-sm">
                    <div class="row align-items-center">
                        @if (count($productDetail->variation->intensity) >= 1)
                        <!-- Progress Circle -->
                        <div class="col-4 text-center">
                            <div class="progress-container mx-auto mb-0">
                                <svg viewBox="0 0 100 100" class="progress-circle">
                                    <!-- Placeholder for dynamic segments -->
                                </svg>
                                <div class="progress-text" id="progress-text">0/0</div>
                            </div>
                            <span><b>{{__('INTENSITY')}}</b></span>
                        </div>
                        @endphp
                        <!-- First Icon -->
                        @if ($productDetail->variation->capacities->count() >= 1)
                        @php
                            $variationCapacity = $productDetail->variation->capacities->first()->variationCapacityTranslation->name;
                            $variationCapacityCode = $productDetail->variation->capacities->first()->code;
                        @endphp
                        @if (preg_match('/^#cp_\d+/', $variationCapacityCode)) <!-- Check for #cp_* pattern -->
                        @php
                            // Extract the number (capsules) from the size
                            preg_match('/\d+/', $variationCapacity, $matches);
                            $capsuleCount = $matches[0] ?? '75'; // Default to 100 if no match found
                        @endphp
                        <div class="col-4 text-center">
                            <div class="icon-container">
                                <div class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" viewBox="0 0 16 16">
                                        <path d="M2.95.4a1 1 0 0 1 .8-.4h8.5a1 1 0 0 1 .8.4l2.85 3.8a.5.5 0 0 1 .1.3V15a1 1 0 0 1-1 1H1a1 1 0 0 1-1-1V4.5a.5.5 0 0 1 .1-.3zM7.5 1H3.75L1.5 4h6zm1 0v3h6l-2.25-3zM15 5H1v10h14z" />
                                    </svg>
                                    <span class="icon-text">{{ $capsuleCount }}</span>
                                    <span class="icon-text-12">{{__('Capsules')}}</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                
                        <!-- Second Icon -->
                        @if ($productDetail->variation->sizes->count() >= 1)
                        @php
                            $variationSize = $productDetail->variation->sizes->first()->variationSizeTranslation->name;
                            $variationSizeCode = $productDetail->variation->sizes->first()->code;
                        @endphp
                        @if (preg_match('/^#cc_\d+/', $variationSizeCode)) <!-- Check for #cp_* pattern -->
                        @php
                            // Extract the number (capsules) from the size
                            preg_match('/\d+/', $variationSize, $matches);
                            $ccCount = $matches[0] ?? '75'; // Default to 100 if no match found
                        @endphp
                        <div class="col-4 text-center">
                            <div class="icon-container">
                                <div class="icon-wrapper">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="icon-svg" viewBox="0 0 16 16">
                                        <path fill-rule="evenodd" d="M.5 6a.5.5 0 0 0-.488.608l1.652 7.434A2.5 2.5 0 0 0 4.104 16h5.792a2.5 2.5 0 0 0 2.44-1.958l.131-.59a3 3 0 0 0 1.3-5.854l.221-.99A.5.5 0 0 0 13.5 6zM13 12.5a2 2 0 0 1-.316-.025l.867-3.898A2.001 2.001 0 0 1 13 12.5M2.64 13.825 1.123 7h11.754l-1.517 6.825A1.5 1.5 0 0 1 9.896 15H4.104a1.5 1.5 0 0 1-1.464-1.175"/>
                                        <path d="m4.4.8-.003.004-.014.019a4 4 0 0 0-.204.31 2 2 0 0 0-.141.267c-.026.06-.034.092-.037.103v.004a.6.6 0 0 0 .091.248c.075.133.178.272.308.445l.01.012c.118.158.26.347.37.543.112.2.22.455.22.745 0 .188-.065.368-.119.494a3 3 0 0 1-.202.388 5 5 0 0 1-.253.382l-.018.025-.005.008-.002.002A.5.5 0 0 1 3.6 4.2l.003-.004.014-.019a4 4 0 0 0 .204-.31 2 2 0 0 0 .141-.267c.026-.06.034-.092.037-.103a.6.6 0 0 0-.09-.252A4 4 0 0 0 3.6 2.8l-.01-.012a5 5 0 0 1-.37-.543A1.53 1.53 0 0 1 3 1.5c0-.188.065-.368.119-.494.059-.138.134-.274.202-.388a6 6 0 0 1 .253-.382l.025-.035A.5.5 0 0 1 4.4.8m3 0-.003.004-.014.019a4 4 0 0 0-.204.31 2 2 0 0 0-.141.267c-.026.06-.034.092-.037.103v.004a.6.6 0 0 0 .091.248c.075.133.178.272.308.445l.01.012c.118.158.26.347.37.543.112.2.22.455.22.745 0 .188-.065.368-.119.494a3 3 0 0 1-.202.388 5 5 0 0 1-.253.382l-.018.025-.005.008-.002.002A.5.5 0 0 1 6.6 4.2l.003-.004.014-.019a4 4 0 0 0 .204-.31 2 2 0 0 0 .141-.267c.026-.06.034-.092.037-.103a.6.6 0 0 0-.09-.252A4 4 0 0 0 6.6 2.8l-.01-.012a5 5 0 0 1-.37-.543A1.53 1.53 0 0 1 6 1.5c0-.188.065-.368.119-.494.059-.138.134-.274.202-.388a6 6 0 0 1 .253-.382l.025-.035A.5.5 0 0 1 7.4.8m3 0-.003.004-.014.019a4 4 0 0 0-.204.31 2 2 0 0 0-.141.267c-.026.06-.034.092-.037.103v.004a.6.6 0 0 0 .091.248c.075.133.178.272.308.445l.01.012c.118.158.26.347.37.543.112.2.22.455.22.745 0 .188-.065.368-.119.494a3 3 0 0 1-.202.388 5 5 0 0 1-.252.382l-.019.025-.005.008-.002.002A.5.5 0 0 1 9.6 4.2l.003-.004.014-.019a4 4 0 0 0 .204-.31 2 2 0 0 0 .141-.267c.026-.06.034-.092.037-.103a.6.6 0 0 0-.09-.252A4 4 0 0 0 9.6 2.8l-.01-.012a5 5 0 0 1-.37-.543A1.53 1.53 0 0 1 9 1.5c0-.188.065-.368.119-.494.059-.138.134-.274.202-.388a6 6 0 0 1 .253-.382l.025-.035A.5.5 0 0 1 10.4.8"/>
                                    </svg>
                                    <span class="icon-text-2">{{ $ccCount }} CC</span>
                                </div>
                            </div>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>
                @endif
                <hr>
                @if ($productDetail->variation->stock)
                <div class="details-filter-row details-row-size nav-dir mt-1">
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
                                    <h3 class="icon-box-title">{{__('Always Authentic')}}</h3><!-- End .icon-box-title -->
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

@if (count($productDetail->variation->intensity) >= 1)
<script>
function createCircularProgressSegments(minValue, maxValue) {
    const svg = document.querySelector('.progress-circle');
    svg.innerHTML = ''; // Clear previous segments

    const radius = 45;
    const gapAngle = 4; // Gap angle in degrees
    const totalAngle = 360; // Full circle
    const segmentAngle = (totalAngle - maxValue * gapAngle) / maxValue;

    for (let i = 0; i < maxValue; i++) {
        const startAngle = i * (segmentAngle + gapAngle);
        const endAngle = startAngle + segmentAngle;

        // Convert polar coordinates to Cartesian points
        const start = polarToCartesian(50, 50, radius, endAngle);
        const end = polarToCartesian(50, 50, radius, startAngle);

        const largeArcFlag = segmentAngle > 180 ? 1 : 0;

        // Define path for the segment
        const pathData = `
            M ${start.x} ${start.y}
            A ${radius} ${radius} 0 ${largeArcFlag} 0 ${end.x} ${end.y}
        `;

        // Background segment
        const bgSegment = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        bgSegment.setAttribute('d', pathData.trim());
        bgSegment.setAttribute('class', 'segment segment-bg');
        svg.appendChild(bgSegment);

        // Filled segment (conditionally filled)
        const fillSegment = document.createElementNS('http://www.w3.org/2000/svg', 'path');
        fillSegment.setAttribute('d', pathData.trim());
        fillSegment.setAttribute('class', 'segment segment-filled');
        fillSegment.style.stroke = i < minValue ? '#003465' : 'transparent';
        svg.appendChild(fillSegment);
    }

    // Update progress text
    const progressText = document.getElementById('progress-text');
    progressText.textContent = `${minValue}/${maxValue}`;
}

// Convert polar coordinates to Cartesian
function polarToCartesian(centerX, centerY, radius, angleInDegrees) {
    const angleInRadians = (angleInDegrees - 90) * (Math.PI / 180);
    return {
        x: centerX + radius * Math.cos(angleInRadians),
        y: centerY + radius * Math.sin(angleInRadians),
    };
}

// Initial render
createCircularProgressSegments({{$productDetail->variation->intensity[0]->min}}, {{$productDetail->variation->intensity[0]->max}});

</script>
@endif

{{-- @if (count($productDetail->variation->intensity) >= 1)
<script>
var intensityData = {{$productDetail->variation->intensity[0]->min}}/{{$productDetail->variation->intensity[0]->max}} * 100; 
var color = null;
function funcColor(val) {
    if (val <= 25) {
        return '#007b00'; // Green
    } else if (val <= 50) {
        return '#f9a603'; // Yellow
    } else if (val <= 75) {
        return '#ff7722'; // Orange
    } else {
        return '#9B1C31'; // Red
    }
}
color = funcColor(intensityData)
var options = {
        series: [intensityData],
        chart: {
        height: 250,
        type: 'radialBar',
        offsetY: -10
    },
    plotOptions: {
        radialBar: {
        startAngle: -90,
        endAngle: 90,
        dataLabels: {
            name: {
            fontSize: '16px',
            color: '#003465',
            offsetY: 45
            },
            value: {
            offsetY: 0,
            fontSize: '20px',
            color: color,
            formatter: function (val) {
                const fraction = (val / 100) * {{$productDetail->variation->intensity[0]->max}};
                return fraction.toFixed(0) + '/' + {{$productDetail->variation->intensity[0]->max}}; // Format the fraction
            }
            }
        }
        }
    },
    fill: {
        colors: [
            function({ value }) {
                if (value <= 25) {
                    return '#007b00'; // Green
                } else if (value <= 50) {
                    return '#f9a603'; // Yellow
                } else if (value <= 75) {
                    return '#ff7722'; // Orange
                } else {
                    return '#9B1C31'; // Red
                }
            }
        ]
    },
    stroke: {
        dashArray: 4
    },
    labels: ['Intensity'],
    };

    var chart = new ApexCharts(document.querySelector("#chart"), options);
    chart.render();
</script>
@endif --}}
@endpush
