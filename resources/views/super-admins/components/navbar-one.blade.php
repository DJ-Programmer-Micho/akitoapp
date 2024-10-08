<div class="app-menu navbar-menu">
    <!-- LOGO -->
    <div class="navbar-brand-box">
        <!-- Dark Logo-->
        <a href="index.html" class="logo logo-dark">
            <span class="logo-sm">
                <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" alt="Akito" height="27">
            </span>
            <span class="logo-lg">
                <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" alt="Akito" height="42">
            </span>
        </a>
        <!-- Light Logo-->
        <a href="index.html" class="logo logo-light">
            <span class="logo-sm">
                <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" alt="Akito" height="27">
            </span>
            <span class="logo-lg">
                <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" alt="Akito" height="42">
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
                <li class="menu-title"><span data-key="t-menu">Menu</span></li>
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
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarPreperties" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarPreperties">
                        <i class="bx bxs-dashboard"></i> <span data-key="t-dashboards">{{__('Properties')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarPreperties">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.brand', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-brands">{{__('Brands')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.category', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-category">{{__('Categories')}}</a>
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
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarProducts" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarProducts">
                        <i class="bx bxs-dashboard"></i> <span data-key="t-products">{{__('Product Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarProducts">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.table', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Table Products')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.create', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-cProduct">{{__('New Products')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.recommend', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-cProduct">{{__('Recommendation Product')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.product.adjust', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-cProduct">{{__('Adjust Product')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarUsers" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarUsers">
                        <i class="bx bxs-dashboard"></i> <span data-key="t-products">{{__('Users Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarUsers">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.users', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-tProduct">{{__('Table Users')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarZones" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarZones">
                        <i class="bx bxs-dashboard"></i> <span data-key="t-products">{{__('Order Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarZones">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.orderManagements', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-zone">{{__('Order Management')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
                <li class="nav-item">
                    <a class="nav-link menu-link" href="#sidebarZones" data-bs-toggle="collapse" role="button" aria-expanded="false" aria-controls="sidebarZones">
                        <i class="bx bxs-dashboard"></i> <span data-key="t-products">{{__('Delivery Management')}}</span>
                    </a>
                    <div class="collapse menu-dropdown" id="sidebarZones">
                        <ul class="nav nav-sm flex-column">
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.deliveryZones', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-zone">{{__('Delivery Zones')}}</a>
                            </li>
                            <li class="nav-item">
                                <a wire:navigate href="{{ route('super.shippingCost', ['locale' => app()->getLocale()]) }}" class="nav-link" data-key="t-zone">{{__('Shipping Costs')}}</a>
                            </li>
                        </ul>
                    </div>
                </li> <!-- end Dashboard Menu -->
            </ul>
        </div>
        <!-- Sidebar -->


    <div class="sidebar-background"></div>
</div>