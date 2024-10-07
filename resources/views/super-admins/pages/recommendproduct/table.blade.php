<div class="page-content">
    <style>
        .item {
            position: relative;
            /* display: block; */
            /* max-width: 100px; */
            border: 1px solid #666;
            /* margin: 0 .3rem .3rem; */
            transition: box-shadow .35s ease;
        }
    </style>
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Products') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Products Table') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <div class="row align-items-center">
                            <div class="col">
                                <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                    <li class="nav-item">
                                        <a class="nav-link @if($statusFilter === 'all') active @endif" style="cursor: pointer" 
                                            wire:click="changeTab('all')" 
                                           role="tab">
                                            {{ __('All') }} 
                                            <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $activeCount + $nonActiveCount }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link @if($statusFilter === 'active') active @endif" style="cursor: pointer"
                                            wire:click="changeTab('active')" 
                                           role="tab">
                                            {{ __('Has Recommendation') }} 
                                            <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">{{ $activeCount }}</span>
                                        </a>
                                    </li>
                                    <li class="nav-item">
                                        <a class="nav-link @if($statusFilter === 'non-active') active @endif" style="cursor: pointer"
                                           wire:click="changeTab('non-active')"
                                           role="tab">
                                            {{ __('Has Not Recommendation') }}
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
                                            <th>{{ __('Product') }}</th>
                                            <th>{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($tableData as $data)
                                            <tr>
                                                <td class="@empty($data->productTranslation->first()->name) text-danger @endif align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="{{ app('cloudfront') . $data->variation->images->first()->image_path }}" alt="{{ $data->name }}" class="img-fluid" style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">{{ $data->productTranslation->first()->name ?? 'Unknown' }}</h6>
                                                            <p class="mb-0">{{ __('Category:') }} {{ $data->categories->first()->categoryTranslation->name }}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span>
                                                        <div class="dropdown">
                                                            <button class="btn btn-soft-secondary btn-sm dropdown" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <i class="ri-more-fill"></i>
                                                            </button>
                                                            <ul class="dropdown-menu dropdown-menu-end" style="">
                                                                <li wire:ignore>
                                                                    <a href="{{ route('super.product.recommend.edit', ['locale' => app()->getLocale(), 'id' => $data->id]) }}" class="dropdown-item edit-list">
                                                                        <i class="fa-regular fa-pen-to-square me-2"></i>{{ __('Edit') }}
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li>
                                                                    <button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#deleteSizeModal" wire:click="removeSize({{ $data->id }})">
                                                                        <i class="fa-regular fa-trash-can me-2"></i>{{ __('Delete') }}</button>
                                                                </li>
                                                            </ul>
                                                        </div>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td colspan="2">
                                                    <div class="owl-carousel">
                                                        @forelse($data->recommendations as $recommendation)         
                                                        <div class="item">
                                                            <div class="text-center h-100">
                                                                <div class="flex-shrink-0 me-2">
                                                                    <img src="{{ app('cloudfront') . $recommendation->variation->images->first()->image_path }}" 
                                                                         alt="{{ $data->name }}" 
                                                                         class="img-fluid mx-auto" 
                                                                         style="max-width: 60px; max-height: 60px; object-fit: cover;">
                                                                </div>
                                                                <div>
                                                                    <h6 class="mb-0">{{ $recommendation->productTranslation->first()->name ?? 'Unknown' }}</h6>
                                                                    <p class="mb-0">{{ __('Category:') }} {{ $recommendation->categories->first()->categoryTranslation->name }}</p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                        @empty
                                                        <div class="text-center">
                                                            <p class="text-danger">Does Not Have Recommendation</p>                                                       
                                                        </div>
                                                        @endforelse
                                                    </div>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="2">
                                                    <div class="py-4 text-center">
                                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:72px;height:72px">
                                                        </lord-icon>
                                                        <h5 class="mt-4">{{ __('Sorry! No Result Found') }}</h5>
                                                    </div>
                                                </td>
                                            </tr>
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
                                    <h5 class="mt-4">{{ __('Sorry! No Result Found') }}</h5>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
                <!-- end card -->
            </div>
            <!-- end col -->
        </div>
    </div>
    
    @push('tProductScripts')
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script>
        function updatePriorityValue(itemId) {
            var input = document.getElementById('priority_' + itemId);
            var updatedPriority = input.value;
            @this.call('updatePriority', itemId, updatedPriority);
        }

        $(document).ready(function(){
            $('.owl-carousel').owlCarousel({
                nav: false,
                dots: false,
                loop: false,
                rtl: {{ app()->getLocale() === 'ar' || app()->getLocale() === 'ku' ? 'true' : 'false' }},
                margin: 20,
                responsive: {
                    0: {
                        items: 2
                    },
                    600: {
                        items: 4
                    },
                    992: {
                        items: 6
                    },
                    1500: {
                        items: 8
                    }
                }
            });
        });
    </script>
    @endpush
</div>
