<!DOCTYPE html>

<html lang="{{(app()->getLocale() != 'kr') ? app()->getLocale() : 'ar'}}">
<head>
    {{-- Meta Tags --}}
    <meta charset="UTF-8">
    <meta name='language' content='AR'>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta name="HandheldFriendly" content="True"/>
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="#dc3545">
    <meta name="theme-color" content="#dc3545">
    <meta name="publisher" content="Michel Shabo">
    <meta name="mobile-web-app-title" content="Akitu">
    <meta name="author" content="Michel Shabo">
    <meta name="copyright" content="MET Iraq">
    <meta name="page-topic" content="Software">
    <meta name="page-type" content="website">
    <meta name="audience" content="Everyone">
    <meta name="robots" content="index, follow"> 
    <meta name='google-site-verification' content='umlVYoC_GB0LKj19BGjAp0DDjU1Jirtq9sVJCgTGgAM'>
    {{-- Sharing Purposes --}}
    <meta name='og:title' content='Akitu'>
    <meta name='og:type' content='Software Company'>
    <meta name='og:url' content='http://akitu-co.com/'>
    {{-- <meta name='og:image' content='https://d7tztcuqve7v9.cloudfront.net/{{app('fixedimage_640x360_half')}}'> --}}
    <meta name='og:site_name' content='Akitu'>
    <meta name='og:description' content='احصل على قائمة الطعام الالكترونية الخاصة بمطعمك وخصصها كما تحب بابسط طريقة وبسعر مميز جدا, وساهم بحصول زبائنك على افضل تجربة'>
    {{-- META TAGS --}}
    <meta name='keywords' content='minemenu, Akitu, ماين منيو, menu iraq, menu erbil, menu resturant, qr code, resturant qr code, finedine, finedinemenu, Akitu iraq, food, drinks, food menu, menu scan, scan menu, منيو, menu generator, food menu generator, قائمة الطعام, food'>
    <meta name="news_keywords" content="minemenu, Akitu, ماين منيو, menu iraq, menu erbil, menu resturant, qr code, resturant qr code, finedine, finedinemenu, Akitu iraq, food, drinks, food menu, menu scan, scan menu, منيو, menu generator, food menu generator, قائمة الطعام, food">
    <!-- apple icons -->
    {{-- <link rel="shortcut icon" href="{{app('logo_72')}}">
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="{{app('logo_144')}}">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="{{app('logo_114')}}">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="{{app('logo_72')}}">
    <link rel="apple-touch-icon-precomposed" sizes="57x57" href="{{app('logo_57')}}">
    <link rel="apple-touch-icon-precomposed" href="{{app('logo_1024')}}"> --}}
    <!-- end of icons -->
    {{-- Title --}}
    <title>Akitu</title>
    {{-- Style --}}
    {{-- <link rel="stylesheet" href="{{asset('/assets/main/css/bootstrap.min.css')}}"> --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="/assets/main/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css" integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w==" crossorigin="anonymous" referrerpolicy="no-referrer" />
</head>

<body>
    <body>	
        <div class="container mt-4">
            <a class="navbar-brand" href="/"> 
                <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" alt="Akitu" width="120">
            </a>	
            <hr class="underline_Logo" style="height: 5px; background-color: #003465">
            @yield('law')
        </div>

        {{-- Js --}}
        {{-- <script src="/assets/dashboard/assets/libs/jquery/dist/jquery.min.js"></script> --}}
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.1/jquery.min.js"></script>
        {{-- <script src="/assets/main/js/bootstrap.min.js"></script> --}}
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
        <script src="/assets/main/js/custom.js"></script>
        @yield('script')
    </body>
</body>
</html>