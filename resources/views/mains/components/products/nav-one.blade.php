<nav aria-label="breadcrumb" class="breadcrumb-nav border-0 mb-0">
    <div class="container d-flex align-items-center">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}">{{__('Home')}}</a></li>
            <li class="breadcrumb-item"><a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">{{__('Shop')}}</a></li>
            <li class="breadcrumb-item"><a href="#">{{__('Product')}}</a></li>
            {{-- <li class="breadcrumb-item active" aria-current="page">Default</li> --}}
        </ol>
    </div><!-- End .container -->
</nav><!-- End .breadcrumb-nav -->