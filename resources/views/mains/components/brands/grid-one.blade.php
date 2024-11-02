<div class="megamenu demo">
    <h4 class="py-1 text-center">{{ __('Brands') }}</h4><!-- End .menu-title -->
    <div class="row">
        @foreach($brands as $brand)
        <div class="col-6 col-sm-4 col-md-3 col-lg-2 mt-1">
            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}"> 
                <div class="demo-bg-wrapper-page">
                    <span class="demo-bg-page" style="background-image: url('{{ app('cloudfront').$brand->image }}');"></span>
                </div>
                {{-- <h6 class="text-center mt-1">{{ $brand->brandTranslation->name }}</h6> --}}
            </a>
        </div><!-- End .demo-item -->
        @endforeach
    </div><!-- End .demo-list -->
</div><!-- End .megamenu -->

<!-- Add this to your CSS -->

<style>
    .demo-bg-wrapper-page {
        position: relative;
        width: 100%; /* Adjust this to control the size of the image container */
        padding-top: 100%; /* This makes the container square */
        overflow: hidden;
        transition: transform 0.3s ease; /* Smooth transition for zoom effect */
    }
    
    .demo-bg-page {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-size: cover; /* Ensure the image covers the container */
        background-position: center;
        transition: transform 0.3s ease; /* Smooth transition for zoom effect */
    }
    
    /* Zoom effect on hover */
    .demo-bg-wrapper-page:hover .demo-bg-page {
        transform: scale(1.1); /* Scale up the image by 10% */
    }
    </style>
