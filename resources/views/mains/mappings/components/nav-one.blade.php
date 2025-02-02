{{--file path: resources/views/mains/mappings/Components/nav-one.blade.php --}}
<div class="header-bottom sticky-header">
    <div class="container">
        <div class="{{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'header-right' : 'header-left' }}">
            <nav class="main-nav">
                <ul class="menu nav-dir sf-arrows">
                    <li class="{{ request()->is(app()->getLocale() . '/') || request()->is(app()->getLocale()) ? 'active' : '' }}">
                        <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}">{{ __('Home') }}</a>
                    </li>
                    <li class="{{ request()->is(app()->getLocale() . '/shop') ? 'active' : '' }}">
                        <a class="nav-font" href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">{{ __('Shop') }}</a>
                    </li>
                    <li class="megamenu-container {{ request()->is(app()->getLocale() . '/categories') ? 'active' : '' }}">
                        <a class="nav-font" href="{{ route('business.category', ['locale' => app()->getLocale()]) }}" class="sf-with-ul">{{ __('Categories') }}</a>
                    
                        <div class="megamenu demo">
                            <div class="menu-col">
                                <div class="menu-title">{{ __('Choose Your Category') }}</div><!-- End .menu-title -->
                                <div class="demo-list">
                                    @foreach($categories as $category)
                                    <div class="demo-item">
                                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $category->id]) }}">
                                            <div class="image-wrapper-100">
                                                <span class="demo-bg-100" style="background-image: url('{{ app('cloudfront').$category->image }}');" alt="{{ $category->image }}"></span>
                                            </div>
                                            <span class="demo-title">{{ $category->categoryTranslation->name }}</span>
                                        </a>
                                    </div><!-- End .demo-item -->
                                    @endforeach
                                </div><!-- End .demo-list -->
                            </div><!-- End .menu-col -->
                        </div><!-- End .megamenu -->
                    </li>
                    
                    <li class="megamenu-container {{ request()->is(app()->getLocale() . '/brands') ? 'active' : '' }}">
                        <a class="nav-font" href="{{ route('business.brand', ['locale' => app()->getLocale()]) }}" class="sf-with-ul">{{ __('Brands') }}</a>
                    
                        <div class="megamenu demo">
                            <div class="menu-col">
                                <div class="menu-title">{{ __('Choose Your Brand') }}</div><!-- End .menu-title -->
                                <div class="demo-list">
                                    @foreach($brands as $brand)
                                    <div class="demo-item">
                                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}">
                                            <div class="image-wrapper-100">
                                                <span class="demo-bg-100" style="background-image: url('{{ app('cloudfront').$brand->image }}');" alt="{{ $brand->image }}"></span>
                                            </div>
                                            <span class="demo-title">{{ $brand->brandTranslation->name }}</span>
                                        </a>
                                    </div><!-- End .demo-item -->
                                    @endforeach
                                </div><!-- End .demo-list -->
                            </div><!-- End .menu-col -->
                        </div><!-- End .megamenu -->
                    </li>
                    
                    {{-- <li class="{{ request()->is(app()->getLocale() . '/spare') ? 'active' : '' }}">
                        <a class="nav-font" href="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}">{{ __('Spare Parts') }}</a>
                    </li> --}}
                </ul><!-- End .menu -->
            </nav><!-- End .main-nav -->

            <button class="mobile-menu-toggler">
                <span class="sr-only">Toggle mobile menu</span>
                <i class="icon-bars"></i>
            </button>
        </div><!-- End .header-left -->

        {{-- <div class="header-right">
            <i class="la la-lightbulb-o"></i><p>Clearance Up to 30% Off</span></p>
        </div> --}}
    </div><!-- End .container -->
</div><!-- End .header-bottom -->

<style>
.image-wrapper-100 {
    position: relative;
    width: 100%;
    padding-top: 100%; /* This ensures the wrapper maintains a square aspect ratio */
    overflow: hidden;
    transition: transform 0.3s ease; /* Smooth transition for scaling */
}

.demo-bg-100 {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-size: cover; /* Ensure the image covers the entire container */
    background-position: center; /* Center the image */
    transition: transform 0.3s ease; /* Smooth transition for scaling */
}

/* Zoom effect on hover */
.image-wrapper-100:hover .demo-bg-100 {
    transform: scale(1.1); /* Scale up the image by 10% */
}


/* Optional: Adjust size for responsiveness */
@media (max-width: 767px) {
    .image-wrapper-100 {
        padding-top: 75%; /* Adjust padding for smaller screens if needed */
    }
}

</style>
