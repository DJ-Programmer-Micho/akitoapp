<div class="container top mt-5">
    <div class="heading my-3">
            <h2 class="title text-center">{{__('Categories')}}</h2><!-- End .title -->
    </div><!-- End .heading -->

    <div class="tab-content tab-content-carousel just-action-icons-sm">
        <div class="row featured-products-loader">
            <div class="slick-carousel skeleton-carousel justify-content-center">
                <!-- Skeleton Slide -->
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
                <div class="skeleton-slide"></div>
            </div>
        </div>
        <div class="owl-carousel owl-full carousel-equal-height carousel-with-shadow" data-toggle="owl" 
            data-owl-options='{
                "nav": true, 
                "dots": true,
                "rtl": {{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'true' : 'false' }},
                "lazyLoad": true,
                {{-- "margin": 20, --}}
                "loop": false,
                "responsive": {
                    "0": {
                        "items":4
                    },
                    "480": {
                        "items":4
                    },
                    "768": {
                        "items":5
                    },
                    "992": {
                        "items":7
                    },
                    "1200": {
                        "items":8
                    }
                }
            }'>
            @foreach ($categoiresData as $category)
            <figure class="product-media-custom">
                <div class="image-container">
                    <a href="{{ route('business.productDetail', ['locale' => app()->getLocale(),'slug' => $category->categoryTranslation->slug])}}">
                        <img src="{{ app('cloudfront') . ($category->image ?? 'path/to/default/image.jpg') }}" alt="{{ $category->categoryTranslation->name ?? 'category Image' }}" class="product-image-category">
                    </a>
                </div>
                <div class="text-container">
                    <h6>{{$category->categoryTranslation->name}}</h6>
                </div>
            </figure>
        @endforeach
        </div><!-- End .owl-carousel -->
    </div><!-- End .tab-content -->
</div><!-- End .container -->
<style>
.product-media-custom {
    display: flex;
    flex-direction: column; /* Stack children vertically */
    align-items: center; /* Center horizontally */
    justify-content: center; /* Center vertically */
    width: 100%; /* Ensure the container takes full width */
    padding: 5px; /* Optional padding */
    position: relative; /* Required for absolute positioning of ::after */
}

.image-container {
    display: flex;
    justify-content: center; /* Center image horizontally */
    align-items: center; /* Center image vertically if needed */
    width: 100%;
}

.product-image-category {
    max-width: 75px; /* Ensure the image scales responsively */
    height: auto; /* Maintain aspect ratio */
}

.text-container {
    margin-top: 10px; /* Space between image and text */
    text-align: center; /* Center text horizontally */
}

.product-media-custom::after {
    position: absolute;
    top: 40%;
    left: 49%;
    content: " ";
    background: #98cdff;
    width: 75px;
    height: 75px;
    margin: 2px;
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: all 0.5s ease;
    z-index: -2;
}

.owl-item:hover .product-media-custom::after {
    top: 28%;
}

.owl-full .owl-dots {
    bottom: 0;
}
</style>