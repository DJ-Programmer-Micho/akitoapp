<div class="header-middle">
    <div class="container">
        <div class="header-left">
            <a href=" {{ route('business.home', ['locale' => app()->getLocale()]) }}">
                <img src="{{ app('main_logo') }}" alt="Akito" width="140" height="30">
            </a>

        </div>
        <div class="header-center">
            <a href=" {{ route('business.home', ['locale' => app()->getLocale()]) }}" class="logo d-lg-none">
                <img src="{{ app('main_logo') }}" alt="Akito" width="140" height="30">
            </a>
        </div><!-- End .header-left -->

        <div class="header-right">
            <div class="header-search header-search-extended header-search-visible d-none d-lg-block mx-0 px-0">
                <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                <form action="{{ route('business.shop.search',['locale' => app()->getLocale()]) }}" method="get" class="border rounded-pill p-2">
                    <div class="header-search-wrapper search-wrapper-wide">
                        <label for="q" class="sr-only">{{__('Search')}}</label>
                        <input type="text" class="form-control" name="q" id="q" placeholder="{{__('Search product ...')}}" required>
                        <button class="btn btn-primary w-25 mr-4" type="submit"><i class="icon-search"></i></button>
                    </div>
                </form>
                
            </div><!-- End .header-search -->
            @livewire('cart.wishlist-livewire')
            @livewire('cart.cart-livewire')
            <div class="dropdown cart-dropdown re">
                @auth('customer')
                    @if (auth()->guard('customer')->user()->customer_profile && auth()->guard('customer')->user()->customer_profile->avatar)
                        <img src="{{ app('cloudfront').auth()->guard('customer')->user()->customer_profile->avatar }}" 
                            alt="{{ auth()->guard('customer')->user()->customer_profile->first_name ?? 'Unknown Customer' }}" 
                            class="img-fluid rounded-circle" 
                            style="min-width: 60px; max-width: 61px; height: 60px; object-fit: cover; border-radius: 50%; border: 3px solid white; object-position: center;">                                                 
                    @else
                        <lord-icon
                            src="https://cdn.lordicon.com/bgebyztw.json"
                            trigger="loop"
                            delay="2000"
                            state="hover-looking-around"
                            colors="primary:#3080e8,secondary:#000000"
                            style="width:40px;height:40px">
                        </lord-icon>                   
                    @endif
                @else
                    <lord-icon
                        src="https://cdn.lordicon.com/bgebyztw.json"
                        trigger="loop"
                        delay="2000"
                        state="hover-looking-around"
                        colors="primary:#3080e8,secondary:#000000"
                        style="width:40px;height:40px">
                    </lord-icon>
                @endauth
                <div class="dropdown-menu p-0" style="width: 150px">
                    <nav class="side-nav">
                        @auth('customer')
                        <ul class="menu-vertical sf-arrows">
                            <li><a href="{{ route('business.account', ['locale' => app()->getLocale()]) }}">{{__('Dashboard')}}</a></li>
                            <li><a href="{{ route('business.whishlist', ['locale' => app()->getLocale()]) }}">{{__('Wishlist')}}</a></li>
                            <li><a href="{{ route('business.viewcart', ['locale' => app()->getLocale()]) }}">{{__('View Cart')}}</a></li>
                            <li><a href="{{ route('business.checkout', ['locale' => app()->getLocale()]) }}">Check Out</a></li>
                            <li class="item-lead"><a href="#signout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a></li>
                        </ul><!-- End .menu-vertical -->
                        @endauth
                        @guest('customer')
                        <ul class="menu-vertical sf-arrows">
                            <li><a href="#signin-modal" data-toggle="modal">Login</a></li>
                            <li><a href="{{ route('business.register', ['locale' => app()->getLocale()]) }}">Register</a></li>
                        </ul><!-- End .menu-vertical -->
                        @endguest
                    </nav><!-- End .side-nav -->
                </div><!-- End .dropdown-menu -->
            </div><!-- End .cart-dropdown -->
            <div class="d-none d-sm-block">
                @auth('customer')
                <p style="color: #003465; white-space: nowrap;">{{auth()->guard('customer')->user()->customer_profile->first_name . ' ' . auth()->guard('customer')->user()->customer_profile->last_name}}</p>
                @endauth
            </div>
        </div>
    </div><!-- End .container -->
</div><!-- End .header-middle -->
