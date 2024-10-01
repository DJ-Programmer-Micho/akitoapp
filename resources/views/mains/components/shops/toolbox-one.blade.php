<div class="toolbox">
    <div class="toolbox-left">
        <div class="toolbox-info">
            {{__('Showing')}} <span>{{ $products->count() }} {{__('of')}} {{ $products->total() }}</span> {{__('Products')}}
        </div><!-- End .toolbox-info -->
    </div><!-- End .toolbox-left -->

    {{-- tool box component --}}
    <div class="toolbox-right">
        <div class="toolbox-sort">
            <label for="sortby">{{__('Sort by:')}}</label>
            <div class="select-custom mx-1">
                @if(request()->is('*/shop'))
                    <form method="GET" action="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">
                @else
                    <form method="GET" action="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}">
                @endif
                {{-- <form method="GET" action="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}"> --}}
                    <select name="sortby" class="form-control" onchange="this.form.submit()">
                    <option value="priority" {{ request()->query('sortby') == 'priority' ? 'selected' : '' }}>{{__('Default')}}</option>
                    <option value="price_asc" {{ request()->query('sortby') == 'price_asc' ? 'selected' : '' }}>{{__('Price: Low to High')}}</option>
                    <option value="price_desc" {{ request()->query('sortby') == 'price_desc' ? 'selected' : '' }}>{{__('Price: High to Low')}}</option>
                    <option value="created_at_desc" {{ request()->query('sortby') == 'created_at_desc' ? 'selected' : '' }}>{{__('Newest')}}</option>
                    <option value="created_at_asc" {{ request()->query('sortby') == 'created_at_asc' ? 'selected' : '' }}>{{__('Oldest')}}</option>
                </select>
                </form>
            </div>
        </div><!-- End .toolbox-sort -->
    <div class="toolbox-layout">
        {{-- 4 Columns Layout --}}
        @if(request()->is('*/shop'))
            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'grid' => '4', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '4' ? 'active' : '' }}">
        @else
            <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale(), 'grid' => '4', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '4' ? 'active' : '' }}">
        @endif
        {{-- <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'grid' => '4', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '4' ? 'active' : '' }}"> --}}
            <svg width="22" height="10">
                <rect x="0" y="0" width="4" height="4" />
                <rect x="6" y="0" width="4" height="4" />
                <rect x="12" y="0" width="4" height="4" />
                <rect x="18" y="0" width="4" height="4" />
                <rect x="0" y="6" width="4" height="4" />
                <rect x="6" y="6" width="4" height="4" />
                <rect x="12" y="6" width="4" height="4" />
                <rect x="18" y="6" width="4" height="4" />
            </svg>
        </a>

        {{-- 3 Columns Layout --}}
        {{-- @if(request()->is('*/shop'))
            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'grid' => '3', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '4' ? 'active' : '' }}">
        @else
            <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale(), 'grid' => '3', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '4' ? 'active' : '' }}">
        @endif
            <svg width="16" height="10">
                <rect x="0" y="0" width="4" height="4" />
                <rect x="6" y="0" width="4" height="4" />
                <rect x="12" y="0" width="4" height="4" />
                <rect x="0" y="6" width="4" height="4" />
                <rect x="6" y="6" width="4" height="4" />
                <rect x="12" y="6" width="4" height="4" />
            </svg>
        </a> --}}

        {{-- 2 Columns Layout --}}
        {{-- @if(request()->is('*/shop'))
            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'grid' => '2', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '4' ? 'active' : '' }}">
        @else
            <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale(), 'grid' => '2', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '4' ? 'active' : '' }}">
        @endif
            <svg width="10" height="10">
                <rect x="0" y="0" width="4" height="4" />
                <rect x="6" y="0" width="4" height="4" />
                <rect x="0" y="6" width="4" height="4" />
                <rect x="6" y="6" width="4" height="4" />
            </svg>
        </a> --}}

        @if(request()->is('*/shop'))
            <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'grid' => '1', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '1' ? 'active' : '' }}">
        @else
            <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale(), 'grid' => '1', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '1' ? 'active' : '' }}">
        @endif
        {{-- <a href="{{ route('business.productShop', ['locale' => app()->getLocale(), 'grid' => '1', 'sortby' => request()->query('sortby')]) }}" class="btn-layout {{ request()->query('grid') == '1' ? 'active' : '' }}"> --}}
            <svg width="16" height="10">
                <rect x="0" y="0" width="4" height="4" />
                <rect x="6" y="0" width="10" height="4" />
                <rect x="0" y="6" width="4" height="4" />
                <rect x="6" y="6" width="10" height="4" />
            </svg>
        </a>
    </div><!-- End .toolbox-layout -->
    </div><!-- End .toolbox-right -->
</div><!-- End .toolbox -->