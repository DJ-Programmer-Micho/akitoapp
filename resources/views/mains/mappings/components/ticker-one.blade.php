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

    .news-ticker-wrapper {
    background-color: #003465;
    padding: 10px 0;
    overflow: hidden;
}

.news-ticker-wrapper #tx .telex-item {
    display: inline-block;
    white-space: nowrap;
}

.news-ticker-wrapper #tx .telex-item a {
    text-decoration: none;
    color: #fff !important;
}

.news-ticker-wrapper #tx .telex-item:hover {
    color: #fff;
}

</style>

{{-- <div class="item"><a class="text-white" href="">Title 2 Title 2 Title 2 Title 2 Title 2</a></div> --}}
<div class="news-ticker-wrapper">
    <div id="tx2"></div>
</div>

@push('ticker')
<script>
    $(document).ready(function () {
        var qtx = Telex.widget("tx2", { 
        }, [
            {
                id: 'msg1',
                content: '<a class="text-white" href="/en/shop?brands%5B%5D=6&min_price=0&max_price=1000">LAVAZZA COFFEE<i class="fa-solid fa-arrow-right"></i></a>'
            },
            {
                id: 'msg2',
                content: '<a class="text-white" href="/en/shop?grid=4">We Always Bring New Products</a>'
            },
            {
                id: 'msg3',
                content: '<a class="text-white" href="/en/shop?grid=4">Some Have %20 OFF</a>'
            },
            {
                id: 'msg4',
                content: '<a class="text-white" href="/en/shop?brands%5B%5D=7&min_price=0&max_price=1000">MONIN - Flavor Inspiration</a>'
            },
            {
                id: 'msg5',
                content: '<a class="text-white" href="/en/shop?grid=4">Go To Shop and Check Whats New?</a>'
            },
            {
                id: 'msg6',
                content: '<a class="text-white" href="">Chrismas Offer\'s are Coming</a>'
            }
        ]);
    });
</script>

@endpush
