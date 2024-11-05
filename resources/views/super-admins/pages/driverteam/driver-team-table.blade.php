<div class="page-content">
    @include('super-admins.pages.driverteam.driver-team-form',[$title = "Avatar"])
    <style>
        .form-check-label{
            font-weight: 800;
            color: var(--vz-blue)
        }
        .driver{
            font-weight: 800;
            color: var(--vz-orange)
        }
    </style>
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Driver Team')}}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Driver Team')}}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="card">
                        <div class="card-header border-0">
                            <div class="row g-3">
                                <div class="col-xxl-4 col-sm-6">
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="Search for order ID, customer, order status or something..." wire:model="searchTerm">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-2 col-sm-3">
                                    <div>
                                        <select class="form-control" data-choices data-choices-search-false wire:model="statusPaymentMethodFilter">
                                            <option value="">Select Payment</option>
                                            <option value="all">All</option>
                                            <option value="Cash On Delivery">Cash On Delivery</option>
                                            <option value="Credit Card">Credit Card</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-2 col-sm-3 align-items-center">
                                    <div>
                                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#addTeamModal"> <i class="fa-solid fa-people-group"></i>
                                            {{__('Add Team')}}
                                        </button>
                                    </div>
                                </div>
                                <div class="col-xxl-2 col-sm-3 align-items-center">
                                    <div>
                                        <a type="button" class="btn btn-primary w-100" href="{{route('super.driver.team.add', ['locale' => app()->getLocale()])}}"> <i class="fa-solid fa-motorcycle"></i>
                                            {{__('Add Drivers in Team')}}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
        
                        <div>

                        <!-- end card header -->
                        <div class="card-body">
                            @if ($tableData->isNotEmpty())
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-hover table-sm">
                                    <thead>
                                        <tr>
                                            <th class="text-center">{{__('Team Name')}}</th>
                                            <th class="text-center">{{__('Team Members')}}</th>
                                            <th class="text-center">{{__('Create Date')}}</th>
                                            <th class="text-center">{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>                          
                                        @forelse($tableData as $data)
                                            <tr>
                                                <td class="align-middle text-center">
                                                        {{ $data->team_name }}
                                                </td>
                                                <td class="align-middle text-center">
                                                    {{ $data->userDriverTeam->count() }}
                                                </td>
                                                <td class="align-middle text-center">
                                                        {{ $data->created_at }}
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
                                                                <li>
                                                                    <button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#updateTeamModal" wire:click="editTeam({{ $data->id }})">
                                                                        <i class="fa-regular fa-pen-to-square me-2"></i>{{__('Edit Team Name')}}
                                                                    </button>
                                                                </li>
                                                                <li>
                                                                    <a type="button" class="dropdown-item edit-list" href="{{ route('super.driver.team.edit', ['locale' => app()->getLocale(), 'id' => $data->id]) }}">
                                                                        <i class="fa-regular fa-pen-to-square me-2"></i>{{__('Edit Team Members')}}
                                                                    </a>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                <li><button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#deleteUserModal" wire:click="removeUser({{ $data->id }})">
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
                                {{ $tableData->links() }}
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
                    </div>
                    <!-- end card -->
                </div>
                
            </div>
        </div>
    </div>
</div>