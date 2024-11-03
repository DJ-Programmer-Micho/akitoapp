<div class="page-content">
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
                            {{-- @if ($tableData) --}}
                            @if ($tableData->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>{{__('Product')}}</th>
                                            <th>{{__('Price')}}</th>
                                            <th>{{__('Old Price')}}</th>
                                            <th>{{__('Published')}}</th>
                                            <th>{{__('Status')}}</th>
                                            <th>{{__('Priority')}}</th>
                                            <th>{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tableData as $data)
                                            <tr>
                                                <td class="@empty($data->productTranslation->first()->name) text-danger @endif align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            @if ($data->variation->images)
                                                                <img src="{{ app('cloudfront').$data->variation->images->first()->image_path }}" alt="{{ $data->name }}" class="img-fluid" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                                            @else
                                                                <img src="{{ app('logo_1024') }}" alt="Akitu-co" class="img-fluid" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                                            @endif
                                                        </div>
                                                        <div>

                                                            <h6 class="mb-0">{{ $data->productTranslation->first()->name ?? 'unKnown' }}</h6>
                                                            <p class="mb-0">{{__('Category:')}} {{ $data->categories->first()->categoryTranslation->name }}</p>
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
                                                        {{ $data->status ? __('Active') : __('Non-Active') }}
                                                    </span>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <input type="number" id="priority_{{ $data->id }}" value="{{ $data->priority }}" class="form-control bg-dark text-white" style="max-width: 80px">
                                                        <button type="button" class="btn btn-warning btn-icon text-dark"  onclick="updatePriorityValue({{ $data->id }})">
                                                            <i class="fas fa-sort"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span>
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end" style="">
                                                                <li><button class="dropdown-item" type="button" wire:click="updateStatus({{ $data->id }})">
                                                                    @if ( $data->status == 1)
                                                                    <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('De-Active')}}</span>
                                                                    @else
                                                                    <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Active')}}</span>
                                                                    @endif
                                                                    </button>
                                                                </li>
                                                                <li wire:ignore>
                                                                    <a href="{{ route('super.product.edit', ['locale' => app()->getLocale(), 'id' => $data->id]) }}" class="dropdown-item edit-list">
                                                                        <i class="fa-regular fa-pen-to-square me-2"></i>{{__('Edit')}}
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#deleteSizeModal" wire:click="removeSize({{ $data->id }})">
                                                                    <i class="fa-regular fa-trash-can me-2"></i>{{__('Delete')}}</button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </span>
                                                </td>
                                            </tr>
                                        @empty
                                        <div class="tab-pane">
                                            <div class="py-4 text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                                </lord-icon>
                                                <h5 class="mt-4">{{__('Sorry! No Result Found')}}</h5>
                                            </div>
                                        </div>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $tableData->links('pagination::bootstrap-4') }}
                            </div>
                            @else
                            <div class="tab-pane">
                                <div class="py-4 text-center">
                                    <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                    </lord-icon>
                                    <h5 class="mt-4">{{__('Sorry! No Result Found')}}</h5>
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
                <div wire:ignore class="card">
                    <div class="card-header">
                        <div class="d-flex mb-3">
                            <div class="flex-grow-1">
                                <h5 class="fs-16">{{__('Filters')}}</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" wire:click="clearFilters" class="text-decoration-underline">{{__('Clear All')}}</a>
                            </div>
                        </div>
    
                        <div class="filter-choices-input">
                            <input wire:model.debounce.500ms="search" class="form-control" type="text" placeholder="{{__('Search...')}}" />
                        </div>
                    </div>
    
                    <div class="accordion accordion-flush filter-accordion">
                        <div class="card-body border-bottom">
                            <p class="text-muted text-uppercase fs-12 fw-medium mb-4">{{__('Price')}}</p>
                            <div id="product-price-range"></div>
                            <div class="formCost d-flex gap-2 align-items-center mt-3">
                                <input wire:model="minPrice" class="form-control form-control-sm" type="number" placeholder="0" />
                                <span class="fw-semibold text-muted">to</span>
                                <input wire:model="maxPrice" class="form-control form-control-sm" type="number" placeholder="1000" />
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingBrands">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseBrands">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">{{__('Brands')}}</span>
                                </button>
                            </h2>
                            {{-- show --}}
                            <div id="flush-collapseBrands" class="accordion-collapse collapse">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($brands as $index => $brand)
                                            <div class="form-check">
                                                <input wire:model="brandIds" class="form-check-input" type="checkbox" value="{{ $brand->id }}" id="brand-{{ $index }}">
                                                <label class="form-check-label" for="brand-{{ $index }}">{{ $brand->brandtranslation->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingCategories">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseCategories">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">{{__('Categories')}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseCategories" class="accordion-collapse collapse">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($categoriesData as $index => $category)
                                            <div class="form-check">
                                                <input wire:model="categoryIds" class="form-check-input" type="checkbox" value="{{ $category->id }}" id="category-{{ $index }}">
                                                <label class="form-check-label" for="category-{{ $index }}">{{ $category->categoryTranslation->name }}</label>
                                            </div>
                                            <!-- Loop over subcategories -->
                                            @if ($category->subCategory->isNotEmpty())
                                                <div class="ms-3">
                                                    @foreach ($category->subCategory as $subIndex => $subCategory)
                                                        <div class="form-check">
                                                            <input wire:model="subCategoryIds" class="form-check-input" type="checkbox" value="{{ $subCategory->id }}" id="subCategory-{{ $index }}-{{ $subIndex }}">
                                                            <label class="form-check-label" for="subCategory-{{ $index }}-{{ $subIndex }}">
                                                                {{ $subCategory->subCategoryTranslation->name }}
                                                            </label>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- end accordion-item -->
{{--     
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingSubCategories">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSubCategories">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">{{__('Sub-Categories')}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseSubCategories" class="accordion-collapse collapse">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($subCategories as $index => $subCategory)
                                            <div class="form-check">
                                                <input wire:model="subCategoryIds" class="form-check-input" type="checkbox" value="{{ $subCategory->id }}" id="subCategory-{{ $index }}">
                                                <label class="form-check-label" for="subCategory-{{ $index }}">{{ $subCategory->subCategoryTranslation->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div> --}}
                        <!-- end accordion-item -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-colors">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseColors" aria-expanded="false" aria-controls="flush-collapseColors">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">{{ __('Colors') }}</span>
                                </button>
                            </h2>
                        
                            <div id="flush-collapseColors" class="accordion-collapse collapse" aria-labelledby="flush-colors">
                                <div class="accordion-body text-body">
                                    <div class="d-flex flex-column gap-2 filter-check">
                                        <div class="filter-colors d-flex">
                                            @foreach ($colors as $color)
                                                <a href="#" 
                                                   class="color-swatch {{ in_array($color->id, $selectedColors) ? 'selected' : '' }}" 
                                                   style="background: {{ $color->code }};"
                                                   wire:click.prevent="toggleColor({{ $color->id }})"
                                                   aria-label="{{ $color->variationColorTranslation->name }}">
                                                    <!-- Use ARIA attributes for better accessibility -->
                                                    <span class="sr-only">{{ $color->variationColorTranslation->name }}</span>
                                                </a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        
    
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingSizes">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseSizes">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">{{__('Sizes')}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseSizes" class="accordion-collapse collapse">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($sizes as $index => $size)
                                            <div class="form-check">
                                                <input wire:model="sizeIds" class="form-check-input" type="checkbox" value="{{ $size->id }}" id="size-{{ $index }}">
                                                <label class="form-check-label" for="size-{{ $index }}">{{ $size->variationSizeTranslation->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingMaterials">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseMaterials">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">{{__('Materials')}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseMaterials" class="accordion-collapse collapse">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($materials as $index => $material)
                                            <div class="form-check">
                                                <input wire:model="materialIds" class="form-check-input" type="checkbox" value="{{ $material->id }}" id="material-{{ $index }}">
                                                <label class="form-check-label" for="material-{{ $index }}">{{ $material->variationMaterialTranslation->name }}</label>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="accordion-item">
                            <h2 class="accordion-header" id="flush-headingCapacities">
                                <button class="accordion-button bg-transparent shadow-none collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseCapacities">
                                    <span class="text-muted text-uppercase fs-12 fw-semibold">{{__('Capacities')}}</span>
                                </button>
                            </h2>
                            <div id="flush-collapseCapacities" class="accordion-collapse collapse">
                                <div class="accordion-body text-body pt-0">
                                    <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                        @foreach ($capacities as $index => $capacity)
                                            <div class="form-check">
                                                <input wire:model="capacityIds" class="form-check-input" type="checkbox" value="{{ $capacity->id }}" id="capacity-{{ $index }}">
                                                <label class="form-check-label" for="capacity-{{ $index }}">{{ $capacity->variationCapacityTranslation->name }}</label>
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
    @push('tProductScripts')
    <script>
        function updatePriorityValue(itemId) {
            var input = document.getElementById('priority_' + itemId);
            var updatedPriority = input.value;
            @this.call('updatePriority', itemId, updatedPriority);
        }
    </script>
    @endpush