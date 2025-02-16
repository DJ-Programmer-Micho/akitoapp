<div class="page-header text-center" style="background-image: url('{{asset('lang/page-header-bg.jpg')}}')">
    <div class="container">
        @if(request()->is('*/shop'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Shop')}}</span></h1>
        @elseif(request()->is('*/about-us'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('About Us')}}</span></h1>
        @elseif(request()->is('*/contact-us'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Contact Us')}}</span></h1>
        @elseif(request()->is('*/faq'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('F.A.Qs')}}</span></h1>
        @elseif(request()->is('*/payment/fib/*'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('FIB Payment')}}</span></h1>
        @elseif(request()->is('*/payment/zaincash/*'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('ZainCash Payment')}}</span></h1>
        @elseif(request()->is('*/payment/creditcard/*'))
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Credit Card Payment')}}</span></h1>
        @else
        <h1 class="page-title" style="font-family: lavaFont;"><span style="font-size: 50px">{{__('Spare Parts')}}</span></h1>
        @endif
    </div><!-- End .container -->
</div><!-- End .page-header -->