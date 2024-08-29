<div class="toolbox">
    <div class="toolbox-left">
        <div class="toolbox-info">
            {{-- Showing <span>{{ $products->count() }} of {{ $products->total() }}</span> Products --}}
        </div><!-- End .toolbox-info -->
    </div><!-- End .toolbox-left -->

    <div class="toolbox-right">
        <div class="toolbox-sort">
            <label for="sortby">Sort by:</label>
            <div class="select-custom">
                <form method="GET" action="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">
                    <select name="sortby" onchange="this.form.submit()">
                    <option value="priority" {{ request()->query('sortby') == 'priority' ? 'selected' : '' }}>Default</option>
                    <option value="price_asc" {{ request()->query('sortby') == 'price_asc' ? 'selected' : '' }}>Price: Low to High</option>
                    <option value="price_desc" {{ request()->query('sortby') == 'price_desc' ? 'selected' : '' }}>Price: High to Low</option>
                    <option value="created_at_desc" {{ request()->query('sortby') == 'created_at_desc' ? 'selected' : '' }}>Newest</option>
                    <option value="created_at_asc" {{ request()->query('sortby') == 'created_at_asc' ? 'selected' : '' }}>Oldest</option>
                </select>
                </form>
            </div>
        </div><!-- End .toolbox-sort -->
        <div class="toolbox-layout">
            <a href="category-list.html" class="btn-layout">
                <svg width="16" height="10">
                    <rect x="0" y="0" width="4" height="4" />
                    <rect x="6" y="0" width="10" height="4" />
                    <rect x="0" y="6" width="4" height="4" />
                    <rect x="6" y="6" width="10" height="4" />
                </svg>
            </a>

            <a href="category-2cols.html" class="btn-layout">
                <svg width="10" height="10">
                    <rect x="0" y="0" width="4" height="4" />
                    <rect x="6" y="0" width="4" height="4" />
                    <rect x="0" y="6" width="4" height="4" />
                    <rect x="6" y="6" width="4" height="4" />
                </svg>
            </a>

            <a href="category.html" class="btn-layout">
                <svg width="16" height="10">
                    <rect x="0" y="0" width="4" height="4" />
                    <rect x="6" y="0" width="4" height="4" />
                    <rect x="12" y="0" width="4" height="4" />
                    <rect x="0" y="6" width="4" height="4" />
                    <rect x="6" y="6" width="4" height="4" />
                    <rect x="12" y="6" width="4" height="4" />
                </svg>
            </a>

            <a href="category-4cols.html" class="btn-layout active">
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
        </div><!-- End .toolbox-layout -->
    </div><!-- End .toolbox-right -->
</div><!-- End .toolbox -->