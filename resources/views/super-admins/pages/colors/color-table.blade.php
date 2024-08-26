<div class="page-content">
    @include('super-admins.pages.colors.color-form')

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Colors')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Colors')}}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->


                <div>
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="row g-4">
                                <div class="col-sm-auto">
                                    <div>
                                        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addColorModal" data-bs-placement="top" title="{{__('Add Color')}}">
                                            <i class="ri-add-line align-bottom me-1"></i>
                                            {{__('Add Color')}}
                                        </button>
                                    </div>
                                </div>

                                <div class="col-sm">
                                    <div class="d-flex justify-content-sm-end">
                                        <div class="search-box ms-2">
                                            <input type="search" wire:model="search" class="form-control" id="searchProductList" placeholder="{{__('Search Tags...')}}">
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
                                            <th>Color Name</th>
                                            <th>Color Preview</th>
                                            <th>Status</th>
                                            <th>priority</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tableData as $data)
                                        
                                            <tr>
                                                <td class="align-middle">{{ $data->id }}</td>
                                                <td class="@empty($data->variationColorTranslation->name)  text-danger @endif align-middle">{{ $data->variationColorTranslation->name ?? 'unKnown' }}</td>
                                                <td class="align-middle">
                                                    <div style="background-color: {{$data->code}}; width: 25px; height: 25px;" class="rounded-circle border border-white"></div>
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
                                                    <button type="button"
                                                        wire:click="updateStatus({{ $data->id }})"
                                                        class="btn {{ $data->status == 0 ? 'btn-danger' : 'btn-success' }} btn-icon">
                                                        <i class="far {{ $data->status == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                                    </button>
                                                    <button type="button" class="btn  btn-primary btn-icon" 
                                                    data-bs-toggle="modal" data-bs-target="#updateColorModal" data-bs-placement="top" title="{{__('Edit Color')}}" wire:click="editColor({{ $data->id }})"
                                                    >
                                                        <i class="fas fa-edit fa-lg"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-danger btn-icon" 
                                                    data-bs-toggle="modal" data-bs-target="#deleteColorModal" data-bs-placement="top" title="{{__('Delete Color')}}" wire:click="removeColor({{ $data->id }})"
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
</div>
@push('colorScripts')
<script>
    function updatePriorityValue(itemId) {
        var input = document.getElementById('priority_' + itemId);
        var updatedPriority = input.value;
        @this.call('updatePriority', itemId, updatedPriority);
    }
</script>
@endpush