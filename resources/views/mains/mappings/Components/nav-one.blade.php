{{--file path: resources/views/mains/mappings/Components/nav-one.blade.php --}}
<div class="header-bottom sticky-header">
    <div class="container">
        <div class="header-left">
            <nav class="main-nav">
                <ul class="menu sf-arrows">
                    <li class="{{ request()->is(app()->getLocale() . '/') || request()->is(app()->getLocale()) ? 'active' : '' }}">
                        <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}">{{ __('Home') }}</a>
                    </li>
                    <li class="{{ request()->is(app()->getLocale() . '/shop') ? 'active' : '' }}">
                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">{{ __('Shop') }}</a>
                    </li>
                    <li class="megamenu-container {{ request()->is(app()->getLocale() . '/categories') ? 'active' : '' }}">
                        <a href="{{ route('business.category', ['locale' => app()->getLocale()]) }}" class="sf-with-ul">{{ __('Categories') }}</a>

                        <div class="megamenu demo">
                            <div class="menu-col">
                                <div class="menu-title">{{ __('Choose Your Category') }}</div><!-- End .menu-title -->
                                <div class="demo-list">
                                    @foreach($categories as $category)
                                    <div class="demo-item">
                                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $category->id]) }}">
                                            <span class="demo-bg" style="background-image: url('{{ app('cloudfront').$category->image }}');" alt="{{ $category->image }}"></span>
                                            <span class="demo-title">{{ $category->categoryTranslation->name }}</span>
                                        </a>
                                    </div><!-- End .demo-item -->
                                    @endforeach
                                </div><!-- End .demo-list -->
                            </div><!-- End .menu-col -->
                        </div><!-- End .megamenu -->
                    </li>
                    <li class="megamenu-container {{ request()->is(app()->getLocale() . '/brands') ? 'active' : '' }}">
                        <a href="{{ route('business.brand', ['locale' => app()->getLocale()]) }}" class="sf-with-ul">{{ __('Brands') }}</a>

                        <div class="megamenu demo">
                            <div class="menu-col">
                                <div class="menu-title">{{ __('Choose Your Brand') }}</div><!-- End .menu-title -->
                                <div class="demo-list">
                                    @foreach($brands as $brand)
                                    <div class="demo-item">
                                        <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}">
                                            <span class="demo-bg" style="background-image: url('{{ app('cloudfront').$brand->image }}');" alt="{{ $brand->image }}"></span>
                                            <span class="demo-title">{{ $brand->brandTranslation->name }}</span>
                                        </a>
                                    </div><!-- End .demo-item -->
                                    @endforeach
                                </div><!-- End .demo-list -->
                            </div><!-- End .menu-col -->
                        </div><!-- End .megamenu -->
                    </li>
                    <li class="{{ request()->is(app()->getLocale() . '/spare') ? 'active' : '' }}">
                        <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}">{{ __('Spare Parts') }}</a>
                    </li>
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
