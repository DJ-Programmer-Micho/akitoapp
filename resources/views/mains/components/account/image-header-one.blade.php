<div class="page-header text-center" style="background-image: url('{{asset('lang/page-header-bg.jpg')}}')">
    <div class="container">
        @if(request()->is('*/account'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Account')}}</span></h1>
        @elseif(request()->is('*/checkout-list'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Checkout')}}</span></h1>
        @elseif(request()->is('*/wallet/topup'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Top Up')}}</span></h1>
        @elseif(request()->is('*/view-cart-list'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('My Cart')}}</span></h1>
        @elseif(request()->is('*/wishlist-list'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('My Wishlist')}}</span></h1>
        @elseif(request()->is('*/email-verify-otp/*'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Email OTP')}}</span></h1>
        @elseif(request()->is('*/verify-otp/*'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Phone OTP')}}</span></h1>
        @elseif(request()->is('*/uasfdr-oiugo-gfhft-iuyer/*'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Forgot Password')}}</span></h1>
        @elseif(request()->is('*/ytuew-uasfdr-oiugo-gfhft/*'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Reset Password')}}</span></h1>
        @else
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Address')}}</span></h1>
        @endif
    </div><!-- End .container -->
</div><!-- End .page-header -->