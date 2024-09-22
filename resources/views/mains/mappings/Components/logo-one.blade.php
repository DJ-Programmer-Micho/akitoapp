<div class="header-middle">
    <div class="container">
        <div class="header-left">
            <a href=" {{ route('business.home', ['locale' => app()->getLocale()]) }}">
                <img src="{{ app('cloudfront').'web-setting/logo.png' }}" alt="Akito" width="120" height="20">
            </a>

        </div>
        <div class="header-center">
            <a href=" {{ route('business.home', ['locale' => app()->getLocale()]) }}" class="logo d-lg-none">
                <img src="{{ app('cloudfront').'web-setting/logo.png' }}" alt="Akito" width="120" height="20">
            </a>
        </div><!-- End .header-left -->

        <div class="header-right">
            <div class="header-search header-search-extended header-search-visible d-none d-lg-block mx-0 px-0">
                <a href="#" class="search-toggle" role="button"><i class="icon-search"></i></a>
                <form action="#" method="get" class="border rounded-pill p-3">
                    <div class="header-search-wrapper search-wrapper-wide ">
                        <label for="q" class="sr-only">Search</label>
                        <input type="text" class="form-control p-2" name="q" id="q" placeholder="Search product ..." required>
                        <button class="btn btn-primary w-25 mr-3" type="submit"><i class="icon-search"></i></button>
                    </div><!-- End .header-search-wrapper -->
                </form>
            </div><!-- End .header-search -->
            @livewire('cart.wishlist-livewire')
            @livewire('cart.cart-livewire')
            <div class="dropdown cart-dropdown">
                <lord-icon
                    src="https://cdn.lordicon.com/bgebyztw.json"
                    trigger="loop"
                    delay="2000"
                    state="hover-looking-around"
                    colors="primary:#3080e8,secondary:#000000"
                    style="width:40px;height:40px">
                </lord-icon>
                @auth('customer')
                <p style="color: #003465; white-space: nowrap;">{{auth()->guard('customer')->user()->customer_profile->first_name . ' ' . auth()->guard('customer')->user()->customer_profile->last_name}}</p>
                @endauth
                <div class="dropdown-menu p-0">
                    <nav class="side-nav">
                        @auth('customer')
                        <ul class="menu-vertical sf-arrows">
                            <li><a href="{{ route('business.account', ['locale' => app()->getLocale()]) }}">Dashboard</a></li>
                            <li class="item-lead"><a href="#" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">Sign Out</a></li>
                        </ul><!-- End .menu-vertical -->
                        @endauth
                        @guest('customer')
                        <ul class="menu-vertical sf-arrows">
                            <li><a href="#signin-modal" data-toggle="modal">Login</a></li>
                            <li><a href="{{ route('business.account', ['locale' => app()->getLocale()]) }}">Register</a></li>
                        </ul><!-- End .menu-vertical -->
                        @endguest
                    </nav><!-- End .side-nav -->
                </div><!-- End .dropdown-menu -->
            </div><!-- End .cart-dropdown -->
        </div>
    </div><!-- End .container -->
</div><!-- End .header-middle -->
