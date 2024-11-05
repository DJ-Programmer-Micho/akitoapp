<div class="page-content">
    @include('super-admins.pages.tickers.ticker-form',[$title = "Brand Image"])

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Tickers')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Tickers')}}</li>
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
                                            <input type="search" wire:model="search" class="form-control" id="searchProductList" placeholder="{{__('Search Tickers...')}}">
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
                                            <th>{{__('Tickers')}}</th>
                                            <th class="text-center">{{__('Link')}}</th>
                                            <th class="text-center">{{__('Status')}}</th>
                                            <th class="text-center">{{__('priority')}}</th>
                                            <th class="text-center">{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tableData as $data)
                                            <tr>
                                                <td class="@empty($data->tickerTranslation->name) text-danger @endif align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <div>
                                                            <h6 class="mb-0">{{ $data->tickerTranslation->name ?? 'unKnown' }}</h6>
                                                        </div>
                                                    </div>

                                                <td class="align-middle text-center">
                                                    <a target="_blank" href="{{$data->url}}">
                                                        <span class="badge bg-warning-subtle text-warning text-uppercase">{{__('Link')}}</span>
                                                    </a>
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
                                                        <div class="dropdown"><button
                                                                class="btn btn-soft-secondary btn-sm dropdown"
                                                                type="button" data-bs-toggle="dropdown"
                                                                aria-expanded="false"><i
                                                                    class="ri-more-fill"></i></button>
                                                            <ul class="dropdown-menu dropdown-menu-end" style="">
                                                                <li><button class="dropdown-item" type="button" wire:click="updateStatus({{ $data->id }})">
                                                                    {{-- <i class="codicon align-bottom me-2 text-muted"></i> --}}
                                                                    @if ( $data->status == 1)
                                                                    <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('De-Active')}}</span>
                                                                    @else
                                                                    <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Active')}}</span>
                                                                    @endif
                                                                    </button>
                                                                </li>
                                                                <li><button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#updateTickerModal" wire:click="editTicker({{ $data->id }})">
                                                                    <i class="fa-regular fa-pen-to-square me-2"></i>{{__('Edit')}}</button>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#deleteTickerModal" wire:click="removeTicker({{ $data->id }})">
                                                                        <i class="fa-regular fa-trash-can me-2"></i>{{__('Delete')}}
                                                                    </button>
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
                                                <h5 class="mt-4">Sorry! No Result Found</h5>
                                            </div>
                                        </div>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $tableData->links() }}
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
                        <form wire:submit.prevent="saveTicker">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="fs-16">{{ __('Add New Ticker') }}</h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-add-line align-bottom me-1"></i> {{__('Add Ticker')}}
                                    </button>
                                </div>
                            </div>
                        
                            <div class="filter-choices-input">
                                @foreach ($filteredLocales as $locale)
                                    <div class="mb-3">
                                        <label for="tickers.{{ $locale }}" class=" @if($locale != 'en') ar-shift @endif">{{__('In ' . $locale . ' Language')}}</label>
                                        <input type="text" 
                                            class="form-control @if($locale != 'en') ar-shift @endif
                                            @error('tickers.' . $locale) is-invalid @enderror
                                            @if(!$errors->has('tickers.' . $locale) && !empty($tickers[$locale])) is-valid @endif"
                                            wire:model="tickers.{{ $locale }}" placeholder="{{__('Ticker Text')}}">
                                        @error('tickers.' . $locale)
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                @endforeach
                            </div>
                            
                            <div class="mb-3">
                                <label for="url">{{__('Link / Destination')}}</label>
                                <input type="text" 
                                    class="form-control 
                                    @error('url') is-invalid @enderror
                                    @if(!$errors->has('url') && !empty($url)) is-valid @endif"
                                    wire:model="url" placeholder="https://akitu-co.com/">
                                @error('url')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="priority">{{__('Priority')}}</label>
                                <input type="text" 
                                    class="form-control 
                                    @error('priority') is-invalid @enderror
                                    @if(!$errors->has('priority') && !empty($priority)) is-valid @endif"
                                    wire:model="priority" placeholder="Priority">
                                @error('priority')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                @enderror
                            </div>
                            
                            <div class="mb-3">
                                <label for="status">{{__('Status')}}</label>
                                <select 
                                    class="form-control @error('status') is-invalid @enderror @if(!$errors->has('status') && $status !== null) is-valid @endif" 
                                    wire:model="status">
                                    <option value="">{{__('Select Status')}}</option>
                                    <option value="1" @if($status == 1) selected @endif>{{__('Active')}}</option>
                                    <option value="0" @if($status == 0) selected @endif>{{__('Non-Active')}}</option>
                                </select>
                                @error('status')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                @enderror
                            </div>
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