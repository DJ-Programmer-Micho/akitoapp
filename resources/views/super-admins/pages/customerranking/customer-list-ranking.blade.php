<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Customers</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                            <li class="breadcrumb-item active">Customers</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card" id="customerList">
                    <div class="card-header border-bottom-dashed">

                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <div>
                                    <h5 class="card-title mb-0">Customer Ranking</h5>
                                </div>
                            </div>
                            {{-- <div class="col-sm-auto">
                                <div class="d-flex flex-wrap align-items-start gap-2">
                                    <button class="btn btn-soft-danger" id="remove-actions" onClick="deleteMultiple()"><i class="ri-delete-bin-2-line"></i></button>
                                    <button type="button" class="btn btn-primary add-btn" data-bs-toggle="modal" id="create-btn" data-bs-target="#showModal"><i class="ri-add-line align-bottom me-1"></i> Add Customer</button>
                                    <button type="button" class="btn btn-secondary"><i class="ri-file-download-line align-bottom me-1"></i> Import</button>
                                </div>
                            </div> --}}
                        </div>
                    </div>
                    <div class="card-body border-bottom-dashed border-bottom">
                        <form>
                            <div class="row g-3">
                                <div class="col-xxl-6 col-sm-12">
                                    <div class="search-box">
                                        <input type="text" class="form-control search" placeholder="Search for customer, email, phone, status or something..."
                                               wire:model="searchTerm">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-2 col-sm-4">
                                    <div>
                                        <input type="date" class="form-control" wire:model="startDate" placeholder="Start Date">
                                    </div>
                                </div>
                                <div class="col-xxl-2 col-sm-4">
                                    <div>
                                        <input type="date" class="form-control" wire:model="endDate" placeholder="End Date">
                                    </div>
                                </div>
                                <!--end col-->
                                <div class="col-xxl-2 col-sm-4">
                                    <div>
                                        <select class="form-control" data-choices data-choices-search-false wire:model="statusFilter">
                                            <option value="">Status</option>
                                            <option value="1">Active</option>
                                            <option value="0">Block</option>
                                        </select>
                                    </div>
                                </div>
                                <!--end col-->
                                {{-- <div class="col-xxl-1 col-sm-4">
                                    <div>
                                        <button type="button" class="btn btn-primary w-100" onclick="SearchData();"> <i class="ri-equalizer-fill me-1"></i>
                                            Filters
                                        </button>
                                    </div>
                                </div> --}}
                                <!--end col-->
                            </div>
                            <!--end row-->
                        </form>
                    </div>
                                                            {{-- @php
                                            dd($tableData );
                                        @endphp --}}
                    <div class="card-body">
                        <div>
                            <div class="table-responsive table-card mb-1">
                                <table class="table align-middle" id="customerTable">
                                    <thead class="table-light text-muted">
                                        <tr>
                                            <th>Customer</th>
                                            <th>Email</th>
                                            <th>Phone</th>
                                            <th>Joining Date</th>
                                            <th>last Order Date</th>
                                            <th>Total Order</th>
                                            <th>Total Amount</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody class="list form-check-all">
                                        @forelse($tableData as $data)
                                        <tr>
                                            <td class="customer_name @empty($data->customer_profile->first_name) text-danger @endif align-middle">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ $data->customer_profile && $data->customer_profile->avatar ? app('cloudfront').$data->customer_profile->avatar : $customerImg }}" 
                                                    alt="{{ $data->customer_profile->first_name ?? 'Unknown Customer' }}" 
                                                    class="img-fluid rounded-circle" 
                                                    style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 3px solid white; object-position: center;">         
                                                </div>
                                                    <div>
                                                        <h6 class="mb-0">{{ $data->customer_profile->first_name ?? 'Unknown' }} {{ $data->customer_profile->last_name ?? '' }}</h6>
                                                        <p class="mb-0"><span>@</span>{{ $data->username ?? 'Customer' }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="email">{{ $data->email }}</td>
                                            <td class="phone">{{ $data->customer_profile->phone_number }}</td>
                                            <td class="date">{{ $data->created_at }}</td>
                                            <td class="date">{{ $data->orders_max_created_at ?? '-' }}</td>
                                            <td class="totalOrder">{{ $data->orders_count ?? 0}}</td>  <!-- Display total orders -->
                                            <td class="totalAmount">{{ Number::currency($data->orders_sum_total_amount ?? 0) }}</td>  <!-- Display total amount -->
                                            <td class="status">
                                                <span class="badge {{ $data->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                                    {{ $data->status ? __('Active') : __('BLOCK') }}
                                                </span>
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
                                                                <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('BLOCK')}}</span>
                                                                @else
                                                                <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Active')}}</span>
                                                                @endif
                                                                </button>
                                                            </li>
                                                            <li class="dropdown-divider"></li>
                                                            <li><a href="{{ route('super.customerOrder', ['locale' => app()->getLocale(), 'id' => $data->id]) }}" class="dropdown-item edit-list">
                                                                <i class="fa-solid fa-list me-2"></i>{{__('Orders View')}}</a>
                                                            </li>
                                                            {{-- <li><button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#deleteUserModal" wire:click="removeUser({{ $data->id }})">
                                                                <i class="fa-regular fa-trash-can me-2"></i>{{__('Delete')}}</button>
                                                            </li> --}}

                                                        </ul>
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                        @empty
                                        <div class="noresult" style="display: none">
                                            <div class="text-center">
                                                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json" trigger="loop" colors="primary:#121331,secondary:#08a88a" style="width:75px;height:75px"></lord-icon>
                                                <h5 class="mt-2">Sorry! No Result Found</h5>
                                                <p class="text-muted mb-0">We've searched more than 150+ Customers We did not find any Customers for you search.</p>
                                            </div>
                                        </div>
                                        @endforelse
                                    </tbody>
                                    
                                </table>
                            </div>
                            <div class="d-flex justify-content-end">
                                {{ $tableData->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->