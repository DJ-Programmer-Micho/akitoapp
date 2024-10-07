<nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
    <div class="container">
        <ol class="breadcrumb">
            <!-- Home Breadcrumb Item -->
            <li class="breadcrumb-item">
                <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}">{{ __('Home') }}</a>
            </li>

            <!-- Dynamic Breadcrumb Items -->
            @if(request()->is('*/shop'))
                <li class="breadcrumb-item active">
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">{{ __('Shop') }}</a>
                </li>
            @elseif(request()->is('*/spare'))
                <li class="breadcrumb-item active">
                    <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}">{{ __('Spare Parts') }}</a>
                </li>
            @elseif(request()->is('*/categories'))
                <li class="breadcrumb-item active">
                    <a href="{{ route('business.category', ['locale' => app()->getLocale()]) }}">{{ __('Categories') }}</a>
                </li>
            @elseif(request()->is('*/shop-search'))
                <li class="breadcrumb-item active">
                    <a href="{{ route('business.category', ['locale' => app()->getLocale()]) }}">{{ __('Search') }}</a>
                </li>
            @elseif(request()->is('*/brands'))
                <li class="breadcrumb-item active">
                    <a href="{{ route('business.brand', ['locale' => app()->getLocale()]) }}">{{ __('Brands') }}</a>
                </li>
            @endif
        </ol>
    </div><!-- End .container -->
</nav><!-- End .breadcrumb-nav -->
