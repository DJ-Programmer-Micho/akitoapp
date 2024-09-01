<div class="container">
    <div class="heading text-center mb-4">
        <h2 class="title">Deals & Outlet</h2><!-- End .title -->
        <p class="title-desc">Today’s deal and more</p><!-- End .title-desc -->
    </div><!-- End .heading -->
    <div class="owl-carousel mt-5 mb-5 owl-simple" data-toggle="owl" 
            data-owl-options='{
                "nav": false, 
                "dots": false,
                "margin": 30,
                "loop": true,
                "responsive": {
                    "0": {
                        "items":2
                    },
                    "420": {
                        "items":3
                    },
                    "600": {
                        "items":4
                    },
                    "900": {
                        "items":5
                    },
                    "1024": {
                        "items":6
                    }
                }
            }'>
            @foreach($brands as $brand)
            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}">
                <img src="{{ app('cloudfront').$brand->image }}" alt="{{ $brand->brandtranslation->name }}">
            </a>
            @endforeach
        </div><!-- End .owl-carousel -->
</div><!-- End .container -->