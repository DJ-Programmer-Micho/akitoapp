<nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
    <div class="container">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="index.html">Home</a></li>
            <li class="breadcrumb-item"><a href="#">Pages</a></li>
            <li class="breadcrumb-item active" aria-current="page">About us</li>
        </ol>
    </div><!-- End .container -->
</nav><!-- End .breadcrumb-nav -->
<div class="container">
    <div class="page-header page-header-big text-center" style="background-image: url({{$mainImg}})">
        <h1 class="page-title text-white">About us<span class="text-white">Who we are</span></h1>
    </div><!-- End .page-header -->
</div><!-- End .container -->

<div class="page-content pb-0">
    <div class="container">
        <div class="row">
            <div class="col-lg-6 mb-3 mb-lg-0">
                <h2 class="title">Our Vision</h2><!-- End .title -->
                <p>To be the leading global destination for exceptional coffee experiences, inspiring a passionate community of coffee enthusiasts and providing unparalleled quality, convenience, and innovation.</p>
            </div><!-- End .col-lg-6 -->
            
            <div class="col-lg-6">
                <h2 class="title">Our Mission</h2><!-- End .title -->
                <p>To curate and deliver a diverse range of premium coffee products, from expertly roasted beans to state-of-the-art brewing machines, empowering our customers to craft their perfect cup at home.</p>
            </div><!-- End .col-lg-6 -->
        </div><!-- End .row -->

        <div class="mb-5"></div><!-- End .mb-4 -->
    </div><!-- End .container -->

    <div class="bg-light-2 pt-6 pb-5 mb-6 mb-lg-12">
        <div class="container text-center">
            <div class="row">
                <div class="col-lg-12 mb-3 mb-lg-0">
                    <h2 class="title">Who We Are</h2><!-- End .title -->
                    <p class="lead text-primary mb-3">We are a passionate team of coffee enthusiasts dedicated to bringing you the finest coffee experiences from around the world.</p><!-- End .lead text-primary -->
                    <p class="mb-2">Our mission is to curate and deliver a diverse range of premium coffee products, from expertly roasted beans to state-of-the-art brewing machines. We believe that every cup of coffee tells a story, and we're committed to sharing those stories with you.</p>

                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}" class="btn btn-sm btn-minwidth btn-outline-primary-2">
                        <span>VIEW OUR SHOP</span>
                        <i class="icon-long-arrow-right"></i>
                    </a>
                </div><!-- End .col-lg-5 -->

            </div><!-- End .row -->
        </div><!-- End .container -->
    </div><!-- End .bg-light-2 pt-6 pb-6 -->

    <div class="container">
        <div class="row">
            <div class="col-lg-5">
                <div class="brands-text">
                    <h2 class="title">The world's premium design brands in one destination.</h2><!-- End .title -->
                    <p>Phasellus hendrerit. Pellentesque aliquet nibh nec urna. In nisi neque, aliquet vel, dapibus id, mattis vel, nis</p>
                </div><!-- End .brands-text -->
            </div><!-- End .col-lg-5 -->
            <div class="col-lg-7">
                <div class="brands-display">
                    <div class="row justify-content-center">
                        @foreach($brands as $brand)
                        <div class="col-6 col-sm-4">
                            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}" class="brand-img">
                                <img width="100" src="{{ app('cloudfront').$brand->image }}" alt="{{ $brand->brandTranslation->name }}">
                            </a>
                        </div><!-- End .col-sm-4 -->
                        @endforeach
                    </div><!-- End .row -->
                </div><!-- End .brands-display -->
            </div><!-- End .col-lg-7 -->
        </div><!-- End .row -->

        <hr class="mt-4 mb-6">
    </div><!-- End .container -->
{{-- 
    <div class="mb-2"></div><!-- End .mb-2 -->

    <div class="about-testimonials bg-light-2 pt-6 pb-6">
        <div class="container">
            <h2 class="title text-center mb-3">What Customer Say About Us</h2><!-- End .title text-center -->

            <div class="owl-carousel owl-simple owl-testimonials-photo" data-toggle="owl" 
                data-owl-options='{
                    "nav": false, 
                    "dots": true,
                    "margin": 20,
                    "loop": false,
                    "responsive": {
                        "1200": {
                            "nav": true
                        }
                    }
                }'>
                <blockquote class="testimonial text-center">
                    <img src="assets/images/testimonials/user-1.jpg" alt="user">
                    <p>“ Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Pellentesque aliquet nibh nec urna. <br>In nisi neque, aliquet vel, dapibus id, mattis vel, nisi. Sed pretium, ligula sollicitudin laoreet viverra, tortor libero sodales leo, eget blandit nunc tortor eu nibh. Nullam mollis. Ut justo. Suspendisse potenti. ”</p>
                    <cite>
                        Jenson Gregory
                        <span>Customer</span>
                    </cite>
                </blockquote><!-- End .testimonial -->

                <blockquote class="testimonial text-center">
                    <img src="assets/images/testimonials/user-2.jpg" alt="user">
                    <p>“ Impedit, ratione sequi, sunt incidunt magnam et. Delectus obcaecati optio eius error libero perferendis nesciunt atque dolores magni recusandae! Doloremque quidem error eum quis similique doloribus natus qui ut ipsum.Velit quos ipsa exercitationem, vel unde obcaecati impedit eveniet non. ”</p>

                    <cite>
                        Victoria Ventura
                        <span>Customer</span>
                    </cite>
                </blockquote><!-- End .testimonial -->
            </div><!-- End .testimonials-slider owl-carousel -->
        </div><!-- End .container -->
    </div><!-- End .bg-light-2 pt-5 pb-6 --> --}}
</div><!-- End .page-content -->