<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" data-layout="vertical" data-topbar="light" data-sidebar-size="lg" data-sidebar="dark" data-sidebar-image="none" data-preloader="disable" data-sidebar-visibility="show" data-layout-style="default" data-bs-theme="dark" data-layout-width="fluid" data-layout-position="fixed">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="csrf-token" content="{{csrf_token()}}">
        <link rel="stylesheet" href="{{asset('main/assets/vendor/line-awesome/line-awesome/line-awesome/css/line-awesome.min.css')}}">
        <!--Swiper slider css-->
        <link href="{{asset('dashboard/libs/swiper/swiper-bundle.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- Layout config Js -->
        <script src="{{asset('dashboard/js/layout.js')}}"></script>
        <!-- Bootstrap Css -->
        <link href="{{asset('dashboard/css/bootstrap.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('dashboard/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('dashboard/css/app.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- custom Css-->
        <link href="{{asset('dashboard/css/custom.min.css')}}" rel="stylesheet" type="text/css" />

        <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/js/all.min.js" integrity="sha512-6sSYJqDreZRZGkJ3b+YfdhB3MzmuP9R7X1QZ6g5aIXhRvR1Y/N/P47jmnkENm7YL3oqsmI6AK+V6AD99uWDnIw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
        <link href="{{asset('dashboard/css/toaster.css')}}" rel="stylesheet" type="text/css">
        <title>{{ $title ?? 'Admin Panel Sign In' }}</title>
        <style>
            .ar-shift {
                direction: rtl;
                text-align: right;
            }
            .auth-one-bg {
                background-image: url('https://images.pexels.com/photos/428310/pexels-photo-428310.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1')!important;
            }
            .shape {
                z-index: 1;
            }
            .auth-one-bg .bg-overlay {
                background: -webkit-gradient(linear, left top, right top, from(#131d31), to(#072e7c));
                background: linear-gradient(to right, #4c7ee3, #6691e7);
                opacity: .6;
            }
            .card{
                z-index: 2;
            }
        </style>
        @livewireStyles
    </head>
    <body>

        <div class="auth-page-content">
            <div class="container">
                <x-super-admins.auth.auth-header-one />
                @yield('super-admin-auth')
            </div>
        </div>


    <!-- JAVASCRIPT -->
    <script src="{{asset('dashboard/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
    <script src="{{asset('dashboard/libs/simplebar/simplebar.min.js')}}"></script>
    <script src="{{asset('dashboard/libs/node-waves/waves.min.js')}}"></script>
    <script src="{{asset('dashboard/libs/feather-icons/feather.min.js')}}"></script>
    <script src="{{asset('dashboard/js/pages/plugins/lord-icon-2.1.0.js')}}"></script>
    <script src="{{asset('dashboard/js/plugins.js')}}"></script>
    <!-- apexcharts -->
    <script src="{{asset('dashboard/libs/apexcharts/apexcharts.min.js')}}"></script>
    <!-- Vector map-->
    <script src="{{asset('dashboard/libs/jsvectormap/js/jsvectormap.min.js')}}"></script>
    <script src="{{asset('dashboard/libs/jsvectormap/maps/world-merc.js')}}"></script>
    <!--Swiper slider js-->
    <script src="{{asset('dashboard/libs/swiper/swiper-bundle.min.js')}}"></script>
    <!-- Dashboard init -->
    <script src="{{asset('dashboard/js/pages/dashboard-ecommerce.init.js')}}"></script>
    <!-- App js -->
    <script src="{{asset('dashboard/js/app.js')}}"></script>
    <!-- particles js -->
    <script src="{{asset('dashboard/libs/particles.js/particles.js')}}"></script>
    <!-- particles app js -->
    <script src="{{asset('dashboard/js/pages/particles.app.js')}}"></script>
    <!-- password-addon init -->
    <script src="{{asset('dashboard/js/pages/password-addon.init.js')}}"></script>
    @stack('tproductscript')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    {{-- <script src="https://cdn.jsdelivr.net/npm/jquery.easing@1.4.1/jquery.easing.min.js"></script> --}}
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    @stack('scripts')
    @stack('brandScripts')
    @stack('tagScripts')
    @stack('colorScripts')
    @stack('sizeScripts')
    @stack('materialsScripts')
    @stack('capacitiesScripts')
    @stack('super_script')
    @stack('asideFilter')
    @stack('tProductScripts')
    @livewireScripts
    <form id="languageForm" action="{{ route('setLocale') }}" method="post">
        @csrf
        <input type="hidden" name="locale" id="selectedLocale" value="{{ app()->getLocale() }}">
    </form>
    
    <script>
        function changeLanguage(locale) {
            document.getElementById('selectedLocale').value = locale;
            document.getElementById('languageForm').submit();
        }
    </script>
    
    <script>
        window.addEventListener('alert', event => { 
            toastr[event.detail.type](event.detail.message, 
            event.detail.title ?? ''), toastr.options = {
                "closeButton": true,
                "progressBar": true,
            }
        });
    </script>

</body>
</html>