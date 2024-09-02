<div class="heading text-center mb-4">
    <h2 class="title">Brands</h2><!-- End .title -->
    <p class="title-desc">Today’s deal and more</p><!-- End .title-desc -->
</div><!-- End .heading -->

<!-- Skeleton Loader -->
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
</div>

<!-- Actual Slides -->
<div class="slick-carousel d-none">
    @foreach($brands as $brand)
    <div>
        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}">
            <img src="{{ app('cloudfront').$brand->image }}" alt="{{ $brand->brandtranslation->name }}">
        </a>
    </div>
    @endforeach
</div><!-- End .slick-carousel -->


<style>
        .slick-slide img {
            width: 100%;
            height: auto;
        }
        .slick-slider {
            width: 100%;
            overflow: hidden;
        }
        .slick-track {
            display: flex;
            align-items: center;
        }
        .slick-slide {
            margin: 0 15px; /* Adjust the gap between images */
        }
        .slick-slide img {
            display: block;
        }
        .skeleton-carousel {
    display: flex;
    overflow: hidden;
}

.skeleton-slide {
    width: 100px; /* Adjust size as needed */
    height: 100px; /* Adjust size as needed */
    background-color: #e0e0e0;
    margin: 0 10px;
    border-radius: 4px;
    animation: pulse 1.5s infinite ease-in-out;
}

@keyframes pulse {
    0% {
        background-color: #e0e0e0;
    }
    50% {
        background-color: #c0c0c0;
    }
    100% {
        background-color: #e0e0e0;
    }
}

.slick-slide img {
    width: 100%;
    height: auto;
}

</style>
@push('brandSlider')
<script>
 $(document).ready(function() {
    $('.slick-carousel').slick({
        slidesToShow: 12,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 0,
        speed: 5000, // Adjust speed as needed
        cssEase: 'linear',
        infinite: true,
        centerMode: false,
        responsive: [
            {
                breakpoint: 1400,
                settings: {
                    slidesToShow: 12
                }
            },
            {
                breakpoint: 1200,
                settings: {
                    slidesToShow: 8
                }
            },
            {
                breakpoint: 900,
                settings: {
                    slidesToShow: 6
                }
            },
            {
                breakpoint: 600,
                settings: {
                    slidesToShow: 5
                }
            },
            {
                breakpoint: 420,
                settings: {
                    slidesToShow: 4
                }
            },
            {
                breakpoint: 0,
                settings: {
                    slidesToShow: 3
                }
            }
        ]
    });

    // Simulate loading time and then show the actual content
    setTimeout(function() {
        $('.skeleton-carousel').hide(); // Hide skeleton loaders
        $('.slick-carousel:not(.skeleton-carousel)').removeClass('d-none'); // Show actual content
    }, 2000); // Simulate loading time (adjust as needed)
});
</script>
@endpush
