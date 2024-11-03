<div class="intro-section">
    <div class="carousel-laods">
        <div class="featured-products-loader-slider">
            <div class="spinner-border text-primary" role="status">
                <span class="sr-only">Loading...</span>
            </div>
        </div>
    </div>
    <div class="intro-slider-container2 slider-container-ratio2">

        <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside" data-toggle="owl" data-owl-options='{
                "nav": false, 
                "dots": true,
                "autoplay": true,
                "autoplayTimeout": 5000,
                "autoplayHoverPause": true
            }'>
            @foreach ($sliders as $slide)
                <div class="intro-slide">
                    <figure class="slide">
                        <picture>
                            <img src="{{ app('cloudfront') . $slide['filename'] }}" alt="Akitu-co">
                        </picture>
                    </figure><!-- End .slide-image -->
                </div><!-- End .intro-slide -->
            @endforeach
        </div>
        {{-- <span class="slider-loader"></span><!-- End .slider-loader --> --}}
    </div><!-- End .intro-slider-container -->
</div><!-- End .intro-section -->
<style>
    .carousel-laods {
        margin-bottom: -12px;
        width: 100%;
        height: 500px;
        position: relative;
    }
.intro-slider-container2 {
    margin-bottom: -12px;
    max-height: 500px;
    overflow: hidden;
    position: relative;
}

.featured-products-loader-slider {
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    height: 100%;
    position: absolute;
    top: 0;
    left: 0;
    background-color: rgba(255, 255, 255, 0.8);
    z-index: 10;
}

.spinner-border {
    width: 3rem;
    height: 3rem;
}
</style>