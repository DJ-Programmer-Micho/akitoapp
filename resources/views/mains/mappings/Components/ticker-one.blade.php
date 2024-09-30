<style>
    .news-ticker-wrapper {
        background-color: #003465;
        padding: 10px 0;
        overflow: hidden;
    }

    .news-ticker-wrapper .owl-carousel .item {
        display: inline-block;
        /* padding: 0 20px; */
        white-space: nowrap;
    }

    .news-ticker-wrapper .owl-carousel .item a {
        text-decoration: none;
        color: #fff !important;
    }

    .news-ticker-wrapper .owl-carousel .item:hover {
        color: #fff;
    }
</style>

{{-- <div class="item"><a class="text-secondary" href="">Title 2 Title 2 Title 2 Title 2 Title 2</a></div> --}}
<div class="news-ticker-wrapper">
    <div class="owl-carousel news-ticker text-center">
        <div class="item">
            <a class="text-secondary" href="/en/shop?brands%5B%5D=6&min_price=0&max_price=1000">LAVAZZA COFFEE<i class="fa-solid fa-arrow-right"></i></a>
        </div>
        <div class="item"><a class="text-secondary" href="/en/shop?grid=4">We Always Bring New Products</a></div>
        <div class="item"><a class="text-secondary" href="/en/shop?grid=4">Some Have %20 OFF</a></div>
        <div class="item"><a class="text-secondary" href="/en/shop?brands%5B%5D=7&min_price=0&max_price=1000">MONIN - Flavor Inspiration</a></div>
        <div class="item"><a class="text-secondary" href="/en/shop?grid=4">Go To Shop and Check Whats New?</a></div>
        <div class="item"><a class="text-secondary" href="">Chrismas Offer's are Coming</a></div>
    </div>
</div>

@push('ticker')
    <script>
        $(document).ready(function(){
        $('.news-ticker').owlCarousel({
            loop: true,
            dots: false,
            // rtl: {{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'true' : 'false' }},
            margin: 50,  // Space between items
            autoplay: true,
            autoplayTimeout: 2000,  // Controls the speed of the ticker
            autoplayHoverPause: true,  // Pauses on hover
            responsive: {
                0: {
                    items: 1
                },
                400: {
                    items: 2
                },
                600: {
                    items: 3
                },
                1000: {
                    items: 5
                }
            }
        });
    });
    </script>
@endpush
