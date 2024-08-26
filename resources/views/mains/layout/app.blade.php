<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="stylesheet" href="{{asset('main/assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css')}}">
        <!-- Plugins CSS File -->
        <link rel="stylesheet" href="{{asset('main/assets/css/bootstrap.min.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/plugins/owl-carousel/owl.carousel.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/plugins/magnific-popup/magnific-popup.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/plugins/jquery.countdown.css')}}">
        {{-- Main CSS File --}}
        <link rel="stylesheet" href="{{asset('main/assets/css/style.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/skins/skin-demo-3.css')}}">
        <link rel="stylesheet" href="{{asset('main/assets/css/demos/demo-3.css')}}">
        <title>{{ $title ?? 'Page Title' }}</title>
        @livewireStyles
    </head>
    <body>
        <div class="page-wrapper">
            <x-mains.mappings.header-one />
            @yield('business-content')
            <x-mains.mappings.footer-one />
        </div><!-- End .page-wrapper -->
        <button id="scroll-top" title="Back to Top"><i class="icon-arrow-up"></i></button>
        <div class="mobile-menu-overlay"></div><!-- End .mobil-menu-overlay -->
        <x-mains.mappings.nav-one />
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
        <!-- Main JS File -->
        <script src="{{asset('main/assets/js/main.js')}}"></script>
        <script src="{{asset('main/assets/js/demos/demo-3.js')}}"></script>
        @livewireScripts
    </body>
</html>