<div class="page-content">
    {{-- @include('super-admins.pages.brands.brand-form',[$title = "Brand Image"]) --}}
<style>
    .filter-colors a {
    position: relative;
    display: block;
    width: 2.4rem;
    height: 2.4rem;
    border-radius: 50%;
    border: .2rem solid #fff;
    margin: 0 .3rem .3rem;
    transition: box-shadow .35s ease;
}
</style>
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Products')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Products Table')}}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-9 col-lg-8">
                <div>
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="row g-4">
                                <div class="col-sm-auto">
                                    <div>
                                        {{-- <a href="apps-ecommerce-add-product.html" class="btn btn-success" id="addproduct-btn"><i class="ri-add-line align-bottom me-1"></i> Add Product</a> --}}
                                    </div>
                                </div>
                                <div class="col-sm">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="search" wire:model="search" class="form-control" id="searchProductList" placeholder="{{__('Search Brands...')}}">
                                            <i class="ri-search-line search-icon"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div>
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link @if($statusFilter === 'all') active @endif" style="cursor: pointer" 
                                                    wire:click="changeTab('all')" 
                                                   role="tab">
                                                    {{__('All')}} 
                                                    <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $activeCount + $nonActiveCount}}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link @if($statusFilter === 'active') active @endif" style="cursor: pointer"
                                                    wire:click="changeTab('active')" 
                                                   role="tab">
                                                    {{__('Active')}} 
                                                    <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $activeCount }}</span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link @if($statusFilter === 'non-active') active @endif" style="cursor: pointer"
                                                   wire:click="changeTab('non-active')"
                                                   role="tab">
                                                    {{__('Non-Active')}}
                                                    <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $nonActiveCount }}</span>
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        <!-- end card header -->
                        <div class="card-body">
                            @if ($tableData)
                            {{-- @if ($tableData->isNotEmpty()) --}}
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>Product</th>
                                            <th>Price</th>
                                            <th>Old Price</th>
                                            <th>Published</th>
                                            <th>Status</th>
                                            <th>Priority</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tableData as $data)
                                        
                                            <tr>
                                                <td class="@empty($data->productTranslation->name) text-danger @endif align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="{{ app('cloudfront').$data->variation->images->first()->image_path }}" 
                                                                 alt="{{ $data->name }}" 
                                                                 class="img-fluid" 
                                                                 style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $data->productTranslation->name ?? 'unKnown' }}</h6>
                                                            <p class="mb-0">Category: {{ $data->categories->first()->categoryTranslation->name }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span>$ {{ $data->variation->price }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($data->variation->discount)
                                                        <span>$ {{ $data->variation->discount }}</span>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span>{{ $data->created_at }}</span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="badge {{ $data->status ? 'bg-success' : 'bg-danger' }} p-2" style="font-size: 0.7rem;">
                                                        {{ $data->status ? 'Active' : 'Non-Active' }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="d-flex">
                                                        <input type="number" id="priority_{{ $data->id }}" value="{{ $data->priority }}" class="form-control bg-dark text-white" style="max-width: 80px">
                                                        <button type="button" class="btn btn-warning btn-icon text-dark"  onclick="updatePriorityValue({{ $data->id }})">
                                                            <i class="fas fa-sort"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span>
                                                        <div class="dropdown"><button
                                                                class="btn btn-soft-secondary btn-sm dropdown"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false"><i
                                                                    class="ri-more-fill"></i></button>
                                                            <ul class="dropdown-menu dropdown-menu-end" style="">
                                                                <li><a class="dropdown-item"
                                                                        href="apps-ecommerce-product-details.html"><i
                                                                            class="ri-eye-fill align-bottom me-2 text-muted"></i>
                                                                        View</a></li>
                                                                <li><a class="dropdown-item edit-list" data-edit-id="1"
                                                                        href="apps-ecommerce-add-product.html"><i
                                                                            class="ri-pencil-fill align-bottom me-2 text-muted"></i>
                                                                        Edit</a></li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><a class="dropdown-item remove-list" href="#"
                                                                        data-id="1" data-bs-toggle="modal"
                                                                        data-bs-target="#removeItemModal"><i
                                                                            class="ri-delete-bin-fill align-bottom me-2 text-muted"></i>
                                                                        Delete</a></li>
                                                            </ul>
                                                        </div>
                                                    </span>
                                                </td>
                                                {{-- <td class="align-middle text-center">
                                                    <button type="button"
                                                        wire:click="updateStatus({{ $data->id }})"
                                                        class="btn {{ $data->status == 0 ? 'btn-danger' : 'btn-success' }} btn-icon">
                                                        <i class="far {{ $data->status == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn  btn-primary btn-icon" 
                                                    data-bs-toggle="modal" data-bs-target="#updateBrandModal" wire:click="editBrand({{ $data->id }})"
                                                    >
                                                        <i class="fas fa-edit fa-lg"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-icon" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteBrandModal" wire:click="removeBrand({{ $data->id }})"
                                                    >
                                                    <i class="fas fa-trash-alt fa-lg"></i>
                                                    </button>
                                                </td> --}}
                                            </tr>
                                        @empty
                                        <div class="tab-pane">
                                            <div class="py-4 text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                                </lord-icon>
                                                <h5 class="mt-4">Sorry! No Result Found</h5>
                                            </div>
                                        </div>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{-- {{ $tableData->links('pagination::bootstrap-4') }} --}}
                            </div>
                            @else
                            <div class="tab-pane">
                                <div class="py-4 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                    </lord-icon>
                                    <h5 class="mt-4">Sorry! No Result Found</h5>
                                </div>
                            </div>
                            @endif
                        <!-- end card body -->
                    </div>
                    </div>
                    </div>
                    <!-- end card -->
                </div>
                
            </div>
            <!-- end col -->
            <div class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex mb-3">
                            <div class="flex-grow-1">
                                <h5 class="fs-16">Filters</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" class="text-decoration-underline" id="clearall">Clear All</a>
                            </div>
                        </div>
    
                        <div class="filter-choices-input">
                            <input class="form-control" data-choices data-choices-removeItem type="text" id="filter-choices-input" value="T-Shirts" />
                        </div>
                    </div>
    
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <p class="text-muted text-uppercase fs-12 fw-medium mb-4">Price</p>
    
                            <div id="product-price-range"></div>
                            <div class="formCost d-flex gap-2 align-items-center mt-3">
                                <input class="form-control form-control-sm" type="text" id="minCost" value="0" /> <span class="fw-semibold text-muted">to</span> <input class="form-control form-control-sm" type="text" id="maxCost" value="1000" />
                            </div>
                        </div>
                        <div class="card-body border-bottom">
                            <div>
                                <p class="text-muted text-uppercase fs-12 fw-medium mb-2">Brands</p>
                                <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                    @foreach ($brands as $index => $brand)
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" value="Boat" id="productBrandRadio5" checked>
                                        <label class="form-check-label" for="productBrandRadio5">{{$brand->brandtranslation->name}}</label>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingBrands">
                                <button class="accordion-button bg-transparent shadow-none" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseBrands" aria-expanded="true" aria-controls="flush-collapseBrands">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">Categories</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                </button>
                            </h2>
    
                            <div id="flush-collapseBrands" class="accordion-collapse collapse show" aria-labelledby="flush-headingBrands">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($categories as $index => $category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="Boat" id="productBrandRadio5" checked>
                                            <label class="form-check-label" for="productBrandRadio5">{{$category->categoryTranslation->name}}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end accordion-item -->
    
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingDiscount">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseDiscount" aria-expanded="true" aria-controls="flush-collapseDiscount">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">Sub Categories</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                </button>
                            </h2>
                            <div id="flush-collapseDiscount" class="accordion-collapse collapse" aria-labelledby="flush-headingDiscount">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($subCategories as $index => $sub_category)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="Boat" id="productBrandRadio5" checked>
                                            <label class="form-check-label" for="productBrandRadio5">{{$sub_category->subCategoryTranslation->name}}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end accordion-item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-colors">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseColors" aria-expanded="false" aria-controls="flush-collapseColors">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">Colors</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                </button>
                            </h2>
    
                            <div id="flush-collapseColors" class="accordion-collapse collapse" aria-labelledby="flush-colors">
                                <div class="accordion-body text-body">
                                    <div class="d-flex flex-column gap-2 filter-check">
                                            {{-- class="selected" --}}
                                            <div class="filter-colors d-flex">
                                            @foreach ($colors as $index => $color)
                                            <a href="#" 
                                            class="color-swatch {{ in_array($color->id, request()->colors ?? []) ? 'selected' : '' }}" 
                                            style="background: {{ $color->code }};">
                                                <span class="sr-only">{{ $color->variationColorTranslation->name }}</span>
                                            </a>
                                            <!-- Hidden input for each color -->
                                            <input type="checkbox" name="colors[]" value="{{ $color->id }}" id="color-filter-{{ $color->id }}" 
                                                style="display: none;" {{ in_array($color->id, request()->colors ?? []) ? 'checked' : '' }}>
                                            @endforeach
                                    </div>
                                    </div>
                                </div>
                            </div>
                        </div>
    
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingSize">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSize" aria-expanded="true" aria-controls="flush-collapseSize">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">Sizes</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                </button>
                            </h2>
                            <div id="flush-collapseSize" class="accordion-collapse collapse" aria-labelledby="flush-headingSize">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($sizes as $index => $size)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="Boat" id="productBrandRadio5" checked>
                                            <label class="form-check-label" for="productBrandRadio5">{{$size->variationSizeTranslation->name}}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingMaterials">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseMaterials" aria-expanded="true" aria-controls="flush-collapseMaterials">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">Materials</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                </button>
                            </h2>
                            <div id="flush-collapseMaterials" class="accordion-collapse collapse" aria-labelledby="flush-headingMaterials">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($materials as $index => $material)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="Boat" id="productBrandRadio5" checked>
                                            <label class="form-check-label" for="productBrandRadio5">{{$material->variationMaterialeTranslation->name}}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingCapacity">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseCapacity" aria-expanded="true" aria-controls="flush-collapseCapacity">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">Capacity</span> <span class="badge bg-success rounded-pill align-middle ms-1 filter-badge"></span>
                                </button>
                            </h2>
                            <div id="flush-collapseCapacity" class="accordion-collapse collapse" aria-labelledby="flush-headingCapacity">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($capacities as $index => $capacity)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" value="Boat" id="productBrandRadio5" checked>
                                            <label class="form-check-label" for="productBrandRadio5">{{$capacity->variationCapacityTranslation->name}}</label>
                                        </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- end card -->
            </div>
        </div>
    </div>
        
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
                </div>
            </div>
        </div>
    </div>
</div>
@push('brandScripts')
<script>
    function updatePriorityValue(itemId) {
        var input = document.getElementById('priority_' + itemId);
        var updatedPriority = input.value;
        @this.call('updatePriority', itemId, updatedPriority);
    }
</script>
@endpush