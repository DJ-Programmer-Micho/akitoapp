<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="/" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{app('logo_57')}}" alt="Akito" height="27">
            </span>
            <span class="logo-lg">
                <img src="{{ app('main_logo') }}" alt="Akito" height="42">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="/" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{app('logo_57')}}" alt="Akito" height="27">
            </span>
            <span class="logo-lg">
                <img src="{{ app('main_logo') }}" alt="Akito" height="42">
            </span>
        </a>
        <button type="button" class="btn btn-sm p-0 fs-20 header-item float-end btn-vertical-sm-hover" id="vertical-hover">
            <i class="ri-record-circle-line"></i>
        </button>
    </div>

    <div id="scrollbar"
        <div class="container-fluid">

            <div id="two-column-menu">
            </div>
            <ul class="navbar-nav" id="navbar-nav">
                <li class="menu-title"><span data-key="t-menu">{{__('Side Bar')}}</span></li>
                @if (hasRole([1]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDashboards" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDashboards">
                        <i class="bx bxs-dashboard"></i> <span data-key="t-dashboards">{{__('Dashboards')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDashboards">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.dashboard', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-ecommerce">{{__('Dashboard')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,2,5,6,7]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPreperties" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPreperties">
                        <i class="bx bx-list-ul"></i> <span data-key="t-dashboards">{{__('Properties')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPreperties">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.brand', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-brands">{{__('Brands')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.soon', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-soons">{{__('Coming Soon')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.category', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-category">{{__('Categories')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.intensity', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-sizes">{{__('Coffee Intensity')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.tag', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tag">{{__('Product Tags')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.color', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-colors">{{__('Product Colors')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.size', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-sizes">{{__('Product Sizes')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.material', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-materials">{{__('Product Materials')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.capacity', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-capacity">{{__('Product Capacity')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,2,5,6,7]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="bx bx-spreadsheet"></i> <span data-key="t-products">{{__('Product Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            @if (hasRole([1,2,5,6,7]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.table', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Table Products')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.create', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-cProduct">{{__('New Products')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.recommend', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-cProduct">{{__('Recommendation Product')}}</a>
                            </li>
                            @endif
                            @if (hasRole([1,2,3,5,6,7]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.adjust', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-cProduct">{{__('Adjust Product')}}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,3,4,5,6,7]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarZones" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarZones">
                        <i class="bx bx-border-bottom"></i> <span data-key="t-products">{{__('Order Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarZones">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.orderManagements', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="o-zone">{{__('Order Management')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.walletManagements', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="w-zone">{{__('Wallet Management')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,3,5,6,7]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarDelivery" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarDelivery">
                        <i class="bx bxs-car"></i> <span data-key="t-products">{{__('Delivery Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarDelivery">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.deliveryZones', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-zone">{{__('Delivery Zones')}}</a>
                            </li>
                            {{-- <li class="nav-item">
                                <a wire:navigate href="{{ route('super.shippingCost', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-zone">{{__('Shipping Costs')}}</a>
                            </li> --}}
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,3]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarCustomers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarCustomers">
                        <i class="bx bx-male-female"></i> <span data-key="t-products">{{__('Customer Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarCustomers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.customerList', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Customers List')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.customerRanking', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Customers Ranking')}}</a>
                            </li>
                            @if (hasRole([1,2]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.customerDiscount', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Customers Discount')}}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,3]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarUsers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsers">
                        <i class="bx bxs-user-account"></i> <span data-key="t-products">{{__('Users Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarUsers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.users', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Table Users')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.driver.team', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Table Driver')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,3,8]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarTasks" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarTasks">
                        <i class="bx bx-task"></i> <span data-key="t-products">{{__('Tasks')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarTasks">
                        <ul class="nav nav-sm flex-column">
                            @if (hasRole([8]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('driver.task', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Driver Task')}}</a>
                            </li>
                            @endif
                            @if (hasRole([1,3]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('driver.task.all', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('All Driver Tasks')}}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
                @if (hasRole([1,2,5,6]))
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarWebSetting" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarWebSetting">
                        <i class="bx bx-wrench"></i> <span data-key="t-products">{{__('Web Setting')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarWebSetting">
                        <ul class="nav nav-sm flex-column">
                            @if (hasRole([1]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.email', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Email')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.recaptcha', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Google Recaptcha')}}</a>
                            </li>
                            @endif
                            @if (hasRole([1,3]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.price', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Checkout Prices')}}</a>
                            </li>
                            @endif
                            @if (hasRole([1,2]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.info', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Information')}}</a>
                            </li>
                            @endif
                            @if (hasRole([1,5,6]))
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.logo', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Logo - App Icon')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.hero', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Hero - Sliders')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.banner', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Category Banner')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('setting.language', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Languages')}}</a>
                            </li>
                            @endif
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                @endif
            </ul>
        </div>
        <!-- Sidebar -->


    <div class="sidebar-background"></div>
</div>