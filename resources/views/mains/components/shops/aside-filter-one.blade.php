{{-- aside-filter-one component --}}
<div class="sidebar sidebar-shop">
    <div class="widget widget-clean">
        <label>Filters:</label>
        @if(request()->is('*/shop'))
        <a href="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}" class="sidebar-filter-clear">Clean All</a>
        @else
        <a href="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}" class="sidebar-filter-clear">Clean All</a>
        @endif
    </div><!-- End .widget widget-clean -->
    @if(request()->is('*/shop'))
    <form id="filtersForm" method="GET" action="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}">
    @else
    <form id="filtersForm" method="GET" action="{{ route('business.productShopSpare', ['locale' => app()->getLocale()]) }}">
    @endif
    {{-- <form id="filtersForm" method="GET" action="{{ route('business.productShop', ['locale' => app()->getLocale()]) }}"> --}}
    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-cat" role="button" aria-expanded="true" aria-controls="widget-cat">
                {{__('Category')}}
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-cat">
            <div class="widget-body">
                <div class="filter-items filter-items-count">
                    @foreach ($categories as $index => $category)
                    <div class="filter-item">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" class="custom-control-input" id="cat-{{ $index + 1 }}" name="categories[]" value="{{ $category->id }}"
                                {{ in_array($category->id, request()->categories ?? []) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="cat-{{$index + 1}}">{{$category->categoryTranslation->name}}</label>
                        </div><!-- End .custom-checkbox -->
                    </div><!-- End .filter-item -->
                    @endforeach
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-sub_cat" role="button" aria-expanded="true" aria-controls="widget-sub_cat">
                {{__('Sub Category')}}
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-sub_cat">
            <div class="widget-body">
                <div class="filter-items filter-items-count">
                    @foreach ($subCategory as $index => $sub_category)
                    <div class="filter-item">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="subcategories[]" value="{{ $sub_category->id }}" class="custom-control-input" id="sub_cat-{{$index + 1}}"
                            {{ in_array($sub_category->id, request()->subcategories ?? []) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="sub_cat-{{$index + 1}}">{{$sub_category->subCategoryTranslation->name}}</label>
                        </div><!-- End .custom-checkbox -->
                    </div><!-- End .filter-item -->
                    @endforeach
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-size" role="button" aria-expanded="true" aria-controls="widget-size">
                Size
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-size">
            <div class="widget-body">
                <div class="filter-items">
                    @foreach ($sizes as $index => $size)
                    <div class="filter-item">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="sizes[]" value="{{ $size->id }}" class="custom-control-input" id="size-{{$index + 1}}"
                            {{ in_array($size->id, request()->sizes ?? []) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="size-{{$index + 1}}">{{$size->variationSizeTranslation->name}}</label>
                        </div><!-- End .custom-checkbox -->
                    </div><!-- End .filter-item -->
                    @endforeach
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-color" role="button" aria-expanded="true" aria-controls="widget-color">
                Colors
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-color">
            <div class="widget-body">
                <div class="filter-colors">
                    {{-- class="selected" --}}
                    @foreach ($colors as $index => $color)
                    <a href="#" 
                       class="color-swatch {{ in_array($color->id, request()->colors ?? []) ? 'selected' : '' }}" 
                       style="background: {{ $color->code }};" 
                       onclick="applyColorFilter(event, {{ $color->id }})">
                        <span class="sr-only">{{ $color->variationColorTranslation->name }}</span>
                    </a>
                    <!-- Hidden input for each color -->
                    <input type="checkbox" name="colors[]" value="{{ $color->id }}" id="color-filter-{{ $color->id }}" 
                           style="display: none;" {{ in_array($color->id, request()->colors ?? []) ? 'checked' : '' }}>
                    @endforeach
                </div><!-- End .filter-colors -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-capacity" role="button" aria-expanded="true" aria-controls="widget-capacity">
                Capacity
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-capacity">
            <div class="widget-body">
                <div class="filter-items">
                    @foreach ($capacities as $index => $capacity)
                    <div class="filter-item">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="capacities[]" value="{{ $capacity->id }}" class="custom-control-input" id="capacity-{{$index + 1}}"
                            {{ in_array($capacity->id, request()->capacities ?? []) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="capacity-{{$index + 1}}">{{$capacity->variationCapacityTranslation->name}}</label>
                        </div><!-- End .custom-checkbox -->
                    </div><!-- End .filter-item -->
                    @endforeach
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-material" role="button" aria-expanded="true" aria-controls="widget-material">
                Material
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-material">
            <div class="widget-body">
                <div class="filter-items">
                    @foreach ($materials as $index => $material)
                    <div class="filter-item">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="materials[]" value="{{ $material->id }}" class="custom-control-input" id="material-{{$index + 1}}"
                            {{ in_array($material->id, request()->materials ?? []) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="material-{{$index + 1}}">{{$material->variationMaterialeTranslation->name}}</label>
                        </div><!-- End .custom-checkbox -->
                    </div><!-- End .filter-item -->
                    @endforeach
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-4" role="button" aria-expanded="true" aria-controls="widget-4">
                Brand
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-4">
            <div class="widget-body">
                <div class="filter-items">
                    @foreach ($brands as $index => $brand)
                    <div class="filter-item">
                        <div class="custom-control custom-checkbox">
                            <input type="checkbox" name="brands[]" value="{{ $brand->id }}" class="custom-control-input" id="brand-{{$index + 1}}"
                            {{ in_array($brand->id, request()->brands ?? []) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="brand-{{$index + 1}}">{{$brand->brandtranslation->name}}</label>
                        </div><!-- End .custom-checkbox -->
                    </div><!-- End .filter-item -->
                    @endforeach
                </div><!-- End .filter-items -->
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->

    <div class="widget widget-collapsible">
        <h3 class="widget-title">
            <a data-toggle="collapse" href="#widget-5" role="button" aria-expanded="true" aria-controls="widget-5">
                Price
            </a>
        </h3><!-- End .widget-title -->

        <div class="collapse show" id="widget-price">
            <div class="widget-body">
                <div id="price-filter">
                    <div id="price-slider"></div>
                    <div id="filter-price-range"></div>
                    <input type="hidden" id="min-price" name="min_price" value="{{ request('min_price', $minPrice) }}">
                    <input type="hidden" id="max-price" name="max_price" value="{{ request('max_price', $maxPrice) }}">
                    
                    <!-- Confirm Button -->
                    <button id="confirm-price-filter" type="button">Confirm</button>
                </div>
            </div><!-- End .widget-body -->
        </div><!-- End .collapse -->
    </div><!-- End .widget -->
    </form>
</div><!-- End .sidebar sidebar-shop -->
<script>
 document.addEventListener('DOMContentLoaded', function () {
    var priceSlider = document.getElementById('price-slider');
    var priceRange = document.getElementById('filter-price-range');
    var minPriceInput = document.getElementById('min-price');
    var maxPriceInput = document.getElementById('max-price');
    var confirmButton = document.getElementById('confirm-price-filter');

    // Get the min and max price values from the server or URL
    var minPrice = parseInt(minPriceInput.value);
    var maxPrice = parseInt(maxPriceInput.value);

    // Get the global min and max price range
    var globalMinPrice = @json($minPrice);
    var globalMaxPrice = @json($maxPrice);
    // Initialize the noUiSlider
    noUiSlider.create(priceSlider, {
        start: [minPrice, maxPrice],
        connect: true,
        step: 1,
        range: {
            'min': globalMinPrice,
            'max': globalMaxPrice
        },
        format: {
            to: function (value) {
                return Math.round(value);
            },
            from: function (value) {
                return Number(value);
            }
        }
    });

    // Update the aria-valuenow and aria-valuetext attributes for slider handles
    function updateAriaValues() {
        var handles = priceSlider.querySelectorAll('.noUi-handle');
        handles[0].setAttribute('aria-valuenow', minPrice);
        handles[0].setAttribute('aria-valuetext', minPrice);
        handles[1].setAttribute('aria-valuenow', maxPrice);
        handles[1].setAttribute('aria-valuetext', maxPrice);
    }

    // Display the current price range and update aria values
    priceSlider.noUiSlider.on('update', function (values, handle) {
        priceRange.innerHTML = '$' + values[0] + ' - $' + values[1];
        minPriceInput.value = values[0];
        maxPriceInput.value = values[1];

        // Update the aria values
        updateAriaValues();
    });

    // When the confirm button is clicked, submit the form
    confirmButton.addEventListener('click', function () {
        document.getElementById('filtersForm').submit();
    });

    // Update aria values on page load
    updateAriaValues();
});
document.getElementById('filtersForm').addEventListener('change', function () {
    const url = new URL(window.location.href);
    const params = new URLSearchParams(new FormData(this));
    window.history.replaceState({}, '', `${url.pathname}?${params.toString()}`);
    this.submit();
});

    
    function applyColorFilter(event, colorId) {
        event.preventDefault(); // Prevent default anchor behavior
    
        // Get the corresponding hidden input element
        const colorInput = document.querySelector(`input#color-filter-${colorId}`);
    
        // Toggle the selected class on the clicked color
        const selectedSwatch = event.currentTarget;
    
        // Check if the color is already selected
        if (colorInput.checked) {
            // If it's already checked, uncheck it and remove the selected class
            colorInput.checked = false;
            selectedSwatch.classList.remove('selected');
        } else {
            // If it's not checked, check it and add the selected class
            colorInput.checked = true;
            selectedSwatch.classList.add('selected');
        }
    
        // Automatically submit the form when a color filter is applied
        document.getElementById('filtersForm').submit();
    }

    document.querySelectorAll('.sort-by').forEach(element => {
    element.addEventListener('click', function() {
        const sortBy = this.getAttribute('data-sortby');
        const url = new URL(window.location.href);
        url.searchParams.set('sortby', sortBy);
        window.location.href = url.toString();
    });
});
    </script>