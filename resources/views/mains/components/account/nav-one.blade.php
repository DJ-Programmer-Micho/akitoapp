<nav aria-label="breadcrumb" class="breadcrumb-nav mb-2">
    <div class="container">
        <ol class="breadcrumb">
            <!-- Home Breadcrumb Item -->
            @if(request()->is('*/checkout-list') || request()->is('*/view-cart-list') || request()->is('*/wishlist-list'))
            <li class="breadcrumb-item">
                <a href="{{ route('business.account', ['locale' => app()->getLocale()]) }}">{{ __('Account') }}</a>
            </li>
            @elseif(request()->is('*/uasfdr-oiugo-gfhft-iuyer/*'))
            <li class="breadcrumb-item">
                <a href="#">{{ __('Reset Password') }}</a>
            </li>
            @elseif(request()->is('*/ytuew-uasfdr-oiugo-gfhft/*'))
            <li class="breadcrumb-item">
                <a href="#">{{ __('Reset Password') }}</a>
            </li>
            @else
            <li class="breadcrumb-item">
                <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}">{{ __('Home') }}</a>
            </li>
            @endif
            <!-- Dynamic Breadcrumb Items -->
            @if(request()->is('*/shop'))
                <li class="breadcrumb-item">
                    <a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">{{ __('Shop') }}</a>
                </li>
            @elseif(request()->is('*/spare'))
                <li class="breadcrumb-item">
                    <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}">{{ __('Spare Parts') }}</a>
                </li>
            @elseif(request()->is('*/categories'))
                <li class="breadcrumb-item">
                    <a href="{{ route('business.category', ['locale' => app()->getLocale()]) }}">{{ __('Categories') }}</a>
                </li>
            @elseif(request()->is('*/brands'))
                <li class="breadcrumb-item">
                    <a href="{{ route('business.brand', ['locale' => app()->getLocale()]) }}">{{ __('Brands') }}</a>
                </li>
            @elseif(request()->is('*/wishlist-list'))
                <li class="breadcrumb-item">
                    <a href="{{ route('business.whishlist', ['locale' => app()->getLocale()]) }}">{{ __('Wishlist') }}</a>
                </li>
            @elseif(request()->is('*/view-cart-list'))
                <li class="breadcrumb-item">
                    <a href="{{ route('business.viewcart', ['locale' => app()->getLocale()]) }}">{{ __('View Carts') }}</a>
                </li>
            @elseif(request()->is('*/checkout-list'))
                <li class="breadcrumb-item">
                    <a href="{{ route('business.checkout', ['locale' => app()->getLocale()]) }}">{{ __('Checkout') }}</a>
                </li>
            @elseif(request()->is('*/wallet/topup'))
                <li class="breadcrumb-item">
                    <a href="{{ route('wallet.topup.form', ['locale' => app()->getLocale()]) }}">{{ __('Top Up') }}</a>
                </li>
            @endif

            <!-- Current Page Breadcrumb Item -->
            @if(request()->is('*/shop'))
            <li class="breadcrumb-item active" aria-current="page">{{__('Products')}}</li>
            @elseif(request()->is('*/checkout-list'))
            <li class="breadcrumb-item active" aria-current="page">{{__('Place Order')}}</li>
            @else
            {{-- <li class="breadcrumb-item active" aria-current="page">{{__('Items')}}</li> --}}
            @endif
        </ol>
    </div><!-- End .container -->
</nav><!-- End .breadcrumb-nav -->
