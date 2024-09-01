<div class="page-header text-center" style="background-image: url('{{asset('lang/page-header-bg.jpg')}}')">
    <div class="container">
        @if(request()->is('*/shop'))
        <h1 class="page-title"><span>{{__('Shop')}}</span></h1>
        @else
        <h1 class="page-title"><span>{{__('Spare Parts')}}</span></h1>
        @endif
    </div><!-- End .container -->
</div><!-- End .page-header -->