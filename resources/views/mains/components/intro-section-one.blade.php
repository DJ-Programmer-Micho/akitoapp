<div class="intro-section pb-3 mb-2">

                <div class="intro-slider-container2 slider-container-ratio2 mb-2 mb-lg-0">
                    <div class="intro-slider owl-carousel owl-simple owl-dark owl-nav-inside" data-toggle="owl" data-owl-options='{
                            "nav": false, 
                            "dots": true,
                            "margin": 10,
                            "autoplay": true,
                            "autoplayTimeout": 5000,
                            "autoplayHoverPause": true
                        }'>
                        <div class="intro-slide">
                            <figure class="slide">
                                <picture>
                                    <img src="{{asset('lang/slide-2.png')}}"  alt="Image Desc">
                                </picture>
                            </figure><!-- End .slide-image -->
                        </div><!-- End .intro-slide -->
                        <div class="intro-slide">
                            <figure class="slide">
                                <picture>
                                    <img src="{{asset('lang/slide-2.png')}}"  alt="Image Desc">
                                </picture>
                            </figure><!-- End .slide-image -->
                        </div><!-- End .intro-slide -->
                    {{-- <span class="slider-loader"></span><!-- End .slider-loader --> --}}
                </div><!-- End .intro-slider-container -->


</div><!-- End .intro-section -->


<style>
.intro-slider-container2 {
    max-height: 320px; /* Max height on larger screens */
    overflow: hidden;
    position: relative; /* To help position the navigation buttons */
}
</style>