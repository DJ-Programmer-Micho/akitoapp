<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <link rel="alternate" hreflang="en" href="http://127.0.0.1:8000/en" />
        <link rel="alternate" hreflang="ar" href="http://127.0.0.1:8000/ar" />
        <link rel="alternate" hreflang="ku" href="http://127.0.0.1:8000/ku" />

        <link rel="stylesheet" href="{{asset('main/assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css')}}">
        <!-- Plugins CSS File -->
        <link rel="stylesheet" href="{{asset('main/assets/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/plugins/owl-carousel/owl.carousel.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/plugins/magnific-popup/magnific-popup.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/plugins/jquery.countdown.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/plugins/nouislider/nouislider.css')}}">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
        <script src="https://cdn.lordicon.com/lordicon.js"></script>
        <link href="{{asset('dashboard/css/toaster.css')}}" rel="stylesheet" type="text/css">
        {{-- Main CSS File --}}
        <link rel="stylesheet" href="{{asset('main/assets/css/style.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/skins/skin-demo-3.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/demos/demo-3.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/custom.css')}}">
        <title>{{ $title ?? 'Page Title' }}</title>
        @stack('styles-password')
        @livewireStyles
    </head>
    <body>
        <div class="page-wrapper">
            <x-mains.mappings.header-one/>
            @yield('business-content')
            <x-mains.mappings.footer-one />
        </div><!-- End .page-wrapper -->
        <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
        <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->
        <x-mains.mappings.nav-one />
        <x-mains.components.login.login />
        {{-- <x-mains.mappings.nav-one /> --}}
        {{-- @livewire('main.auth.form-one') --}}
        {{-- @livewire('main.pop.news-one') --}}
        <!-- Plugins JS File -->
        <script src="{{asset('main/assets/js/jquery.min.js')}}"></script>
        <script src="{{asset('main/assets/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('main/assets/js/jquery.hoverIntent.min.js')}}"></script>
        <script src="{{asset('main/assets/js/jquery.waypoints.min.js')}}"></script>
        <script src="{{asset('main/assets/js/superfish.min.js')}}"></script>
        <script src="{{asset('main/assets/js/owl.carousel.min.js')}}"></script>
        <script src="{{asset('main/assets/js/bootstrap-input-spinner.js')}}"></script>
        <script src="{{asset('main/assets/js/jquery.plugin.min.js')}}"></script>
        <script src="{{asset('main/assets/js/jquery.magnific-popup.min.js')}}"></script>
        <script src="{{asset('main/assets/js/jquery.countdown.min.js')}}"></script>
        <script src="{{asset('main/assets/js/nouislider.min.js')}}"></script>
        <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/fancybox/3.5.7/jquery.fancybox.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js" integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <!-- Main JS File -->
        <script src="{{asset('main/assets/js/main.js')}}"></script>
        <script src="{{asset('main/assets/js/demos/demo-3.js')}}"></script>
        <script src="https://www.google.com/recaptcha/api.js?render={{ env('GOOGLE_RECAPTCHA_KEY') }}"></script>
        @stack("ticker")
        @stack("productView")
        @stack("brandSlider")
        @stack("register")
        @stack("password")
        @stack("tab-script")
        @stack("geo")
        @livewireScripts
<form id="languageForm" action="{{ route('setLocale') }}" method="post">
    @csrf
    <input type="hidden" name="locale" id="selectedLocale" value="{{ app()->getLocale() }}">
</form>
<form id="logout-form" action="{{ route('customer.logout', ['locale' => app()->getLocale()]) }}" method="POST" style="display: none;">
    @csrf
</form>

<script>
    function changeLanguage(locale) {
        document.getElementById('selectedLocale').value = locale;
        document.getElementById('languageForm').submit();
    }
    $(document).ready(function() {
    // This should be triggered when your data is fully loaded
    $('.featured-products-loader').hide();
    $('.carousel-laods').hide();
    $('.featured-products-content').show();
});
</script>
@if(session('alert'))
    <script>
        window.addEventListener('load', function () {
            toastr["{{ session('alert')['type'] }}"]("{{ session('alert')['message'] }}", "", {
                "closeButton": true,
                "progressBar": true,
                "positionClass": "toast-bottom-right",
                "timeOut": "5000"
            });
        });
    </script>
@endif
<script>
    window.addEventListener('alert', event => { 
        toastr[event.detail.type](event.detail.message, 
        event.detail.title ?? ''), toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "debug": false,
            "newestOnTop": false,
            "positionClass": "toast-bottom-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        }
    });
</script>
</body>
</html>