<div class="page-content">
    @include('super-admins.pages.brands.brand-form',[$title = "Brand Image"])

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Brands')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Brands')}}</li>
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
                            @if ($tableData->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th class="w-50">Brand Name</th>
                                            <th>Image</th>
                                            <th class="w-25">Status</th>
                                            <th class="w-25">priority</th>
                                            <th class="w-50">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tableData as $data)
                                        
                                            <tr>
                                                <td class="align-middle">{{ $data->id }}</td>
                                                <td class="@empty($data->brandtranslation->name)  text-danger @endif align-middle">{{ $data->brandtranslation->name ?? 'unKnown' }}</td>
                                                <td class="align-middle text-center">
                                                    <div class="d-flex justify-content-center align-items-center" style="height: 100px;">
                                                        <img src="{{ app('cloudfront').$data->image }}" alt="{{ $data->name }}" class="img-fluid" style="max-width: 100px; max-height: 100px; object-fit: cover;">
                                                    </div>                                                </td>
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
                                                </td>
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
                                {{ $tableData->links('pagination::bootstrap-4') }}
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

            <div @if($de == 0) wire:ignore @endif class="col-xl-3 col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <form wire:submit.prevent="saveBrand">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="fs-16">{{ __('Add New Brand') }} {{$de}}</h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-add-line align-bottom me-1"></i> Add Product
                                    </button>
                                </div>
                            </div>
                        
                            <div class="filter-choices-input">
                                @foreach ($filteredLocales as $locale)
                                    <div class="mb-3">
                                        <label for="brands.{{ $locale }}">In {{ $locale }} Language</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('brands.' . $locale) is-invalid @enderror
                                            @if(!$errors->has('brands.' . $locale) && !empty($brands[$locale])) is-valid @endif"
                                            wire:model="brands.{{ $locale }}" placeholder="Brand Name">
                                        @error('brands.' . $locale)
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mb-3">
                                <label for="priority">Priority</label>
                                <input type="text" 
                                    class="form-control 
                                    @error('priority') is-invalid @enderror
                                    @if(!$errors->has('priority') && !empty($priority)) is-valid @endif"
                                    wire:model="priority" placeholder="Priority">
                                @error('priority')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="status">Status</label>
                                <select 
                                    class="form-control @error('status') is-invalid @enderror @if(!$errors->has('status') && $status) is-valid @endif" 
                                    wire:model="status"
                                >
                                    <option value="">Select Status</option>
                                    <option value="1" @if($status == 1) selected @endif>Active</option>
                                    <option value="0" @if($status == 0) selected @endif>Non-Active</option>
                                </select>
                                @error('status')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            @include('super-admins.pages.components.single-image',[$title = "Brand Image"])
                        </form>
                    </div>
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