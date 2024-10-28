<div class="mobile-menu-container">
    <div class="mobile-menu-wrapper">
        <span class="mobile-menu-close"><i class="icon-close"></i></span>
        
        <form action="{{ route('business.shop.search',['locale' => app()->getLocale()]) }}" method="get" class="mobile-search">
            <label for="mobile-search" class="sr-only">Search</label>
                <input type="text" class="form-control" name="q" id="q" placeholder="{{__('Search product ...')}}" required>
                <button class="btn btn-primary" type="submit"><i class="icon-search"></i></button>
        </form>

        <ul class="nav nav-pills-mobile nav-border-anim" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="mobile-menu-link" data-toggle="tab" href="#mobile-menu-tab" role="tab" aria-controls="mobile-menu-tab" aria-selected="true">{{__("Menu")}}</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="mobile-cats-link" data-toggle="tab" href="#mobile-cats-tab" role="tab" aria-controls="mobile-cats-tab" aria-selected="false">{{__("Languages")}}</a>
            </li>
        </ul>

        <div class="tab-content">
            <div class="tab-pane fade show active" id="mobile-menu-tab" role="tabpanel" aria-labelledby="mobile-menu-link">
                <nav class="mobile-nav">
                    <ul class="mobile-menu">
                        {{-- <li class="{{ request()->is(app()->getLocale() . '/') || request()->is(app()->getLocale()) ? 'active' : '' }}">
                            <a href="{{ route('business.home', ['locale' => app()->getLocale()]) }}">{{ __('Home') }}</a>
                        </li> --}}
                        <li class="{{ request()->is(app()->getLocale() . '/shop') ? 'active' : '' }}">
                            <a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">{{ __('Shop') }}</a>
                        </li>
                        <li class="{{ request()->is(app()->getLocale() . '/spare') ? 'active' : '' }}">
                            <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}">{{ __('Spare Parts') }}</a>
                        </li>
                        <li class="megamenu-container {{ request()->is(app()->getLocale() . '/categories') ? 'active' : '' }}">
                            <a href="{{ route('business.category', ['locale' => app()->getLocale()]) }}" class="sf-with-ul">{{ __('Categories') }}</a>
                            <ul class="nav-dir">
                                @foreach($categories as $category)
                                <li class="p-1">
                                    <a class="p-1" href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'categories[]' => $category->id]) }}">
                                        <span class="demo-bg" style="background-image: url('{{ app('cloudfront').$category->image }}'); padding: 60px" alt="{{ $category->image }}"></span>
                                        <span class="demo-title">{{ $category->categoryTranslation->name }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="megamenu-container {{ request()->is(app()->getLocale() . '/brands') ? 'active' : '' }}">
                            <a href="{{ route('business.brand', ['locale' => app()->getLocale()]) }}" class="sf-with-ul">{{ __('Brands') }}</a>    
                            <ul>
                                @foreach($brands as $brand)
                                <li class="p-1">
                                    <a class="p-1" href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'brands[]' => $brand->id]) }}">
                                        <span class="demo-bg" style="background-image: url('{{ app('cloudfront').$brand->image }}'); padding: 60px" alt="{{ $brand->image }}"></span>
                                        <span class="demo-title">{{ $brand->brandTranslation->name }}</span>
                                    </a>
                                </li>
                                @endforeach
                            </ul>
                        </li>
                        <li class="{{ request()->is(app()->getLocale() . '/wishlist-list') ? 'active' : '' }}">
                            <a href="{{ route('business.whishlist', ['locale' => app()->getLocale()]) }}">{{__('Wishlist')}}</a>
                        </li>
                        <li class="{{ request()->is(app()->getLocale() . '/view-cart-list') ? 'active' : '' }}">
                            <a href="{{ route('business.viewcart', ['locale' => app()->getLocale()]) }}">{{__('View Cart')}}</a>
                        </li>
                    </ul>
                </nav><!-- End .mobile-nav -->
            </div><!-- .End .tab-pane -->
            <div class="tab-pane fade" id="mobile-cats-tab" role="tabpanel" aria-labelledby="mobile-cats-link">
                <nav class="mobile-cats-nav">
                    <ul class="mobile-menu">
                        @foreach (config('app.locales') as $locale)
                        <li>
                            <a class="dropdown-item" onclick="changeLanguage('{{ $locale }}')"><img src="{{asset('lang/'.$locale.'.png')}}" width="20" style="display: inline-block" alt="akito"> <span class="text-white"> {{ __(strtoupper($locale)) }}</span></a>

                        </li>
                        @endforeach
                    </ul><!-- End .mobile-cats-menu -->
                </nav><!-- End .mobile-cats-nav -->
            </div><!-- .End .tab-pane -->
        </div><!-- End .tab-content -->

        <div class="social-icons">
            <a href="{{app('facebookUrl')}}" class="social-icon" target="_blank" title="Facebook"><i class="icon-facebook-f"></i></a>
            <a href="{{app('instagramUrl')}}" class="social-icon" target="_blank" title="Instagram"><i class="icon-instagram"></i></a>
            <a href="{{app('tiktokUrl')}}" class="social-icon" target="_blank" title="TikTok"><i class="fa-brands fa-tiktok"></i></a>
            {{-- <a href="#" class="social-icon" target="_blank" title="Youtube"><i class="icon-youtube"></i></a> --}}
        </div><!-- End .social-icons -->
    </div><!-- End .mobile-menu-wrapper -->
</div><!-- End .mobile-menu-container -->