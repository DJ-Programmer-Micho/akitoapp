<div class="page-content">
    @include('super-admins.pages.users.users-form',[$title = "Avatar"])
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
                    <h4 class="mb-sm-0">{{__('Users')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Users')}}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-8">
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
                                            <input type="search" wire:model="search" class="form-control" id="searchProductList" placeholder="{{__('Search Users...')}}">
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
                                            <th>{{__('User')}}</th>
                                            <th class="text-center">{{__('Roles')}}</th>
                                            <th class="text-center">{{__('Status')}}</th>
                                            <th class="text-center">{{__('Action')}}</th>
                                        </tr>
                                    </thead>
                                    <tbody>                          
                                        @forelse($tableData as $data)
                                            <tr wire:key="data-{{ $data->id }}">
                                                <td class="@empty($data->profile->first_name) text-danger @endif align-middle">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            {{-- <img src="{{ app('cloudfront').$data->variation->images->first()->image_path }}" alt="{{ $data->name }}" class="img-fluid" style="max-width: 60px; max-height: 60px; object-fit: cover;"> --}}
                                                            <img src="{{isset($data->profile->avatar) ? app('cloudfront').$data->profile->avatar : $userImg}}" alt="{{ $data->profile->first_name . ' ' . $data->profile->last_name }}" class="img-fluid" style="max-width: 60px; max-height: 60px; object-fit: cover; border-radius: 50%; border: 1px solid white">
                                                        </div>
                                                        <div>
                                                            {{-- @php
                                                                dd($data->productTranslation);
                                                            @endphp --}}
                                                            <h6 class="mb-0">{{ $data->profile->first_name . ' ' . $data->profile->last_name ?? 'unKnown' }}</h6>
                                                            <p class="mb-0">{{__('Position:')}} {{$data->profile->position}}</p>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center">
                                                    @if ($data->roles->count() === 7)
                                                        <span class="badge bg-primary p-2" style="font-size: 0.7rem;">
                                                            {{ __('ADMIN') }}
                                                        </span>
                                                    @else
                                                        @foreach ($data->roles as $role)
                                                            @php
                                                                // Define a map of roles to classes if needed
                                                                $roleClass = 'bg-secondary'; // Default class
                                                                // You can use role names or other criteria to set specific classes if needed
                                                            @endphp
                                                            <span class="badge {{ $roleClass }} p-2" style="font-size: 0.7rem;">
                                                                {{ $role->name }}
                                                            </span>
                                                        @endforeach
                                                    @endif
                                                </td>
                                                <td class="align-middle text-center">
                                                    <span class="badge {{ $data->status ? 'bg-success' : 'bg-danger' }} p-2" style="font-size: 0.7rem;">
                                                        {{ $data->status ? __('Active') : __('Non-Active') }}
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
                                                                    <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('De-Active')}}</span>
                                                                    @else
                                                                    <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Active')}}</span>
                                                                    @endif
                                                                    </button>
                                                                </li>
                                                                <li><button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#updateUserModal" wire:click="editUser({{ $data->id }})">
                                                                    <i class="fa-regular fa-pen-to-square me-2"></i>{{__('Edit')}}</button>
                                                                </li>
                                                                <li class="dropdown-divider"></li>
                                                                {{-- <li><button type="button" class="dropdown-item edit-list" data-bs-toggle="modal" data-bs-target="#deleteUserModal" wire:click="removeUser({{ $data->id }})">
                                                                    <i class="fa-regular fa-trash-can me-2"></i>{{__('Delete')}}</button>
                                                                </li> --}}

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
                    <!-- end card -->
                </div>
                
            </div>
            <!-- end col -->

            <div @if($de == 0) wire:ignore @endif class="col-lg-4">
                <div class="card">
                    <div class="card-header">
                        <form wire:submit.prevent="saveUser">
                            <div class="d-flex mb-3">
                                <div class="flex-grow-1">
                                    <h5 class="fs-16">{{ __('Add New User') }}</h5>
                                </div>
                                <div class="flex-shrink-0">
                                    <button type="submit" class="btn btn-success">
                                        <i class="ri-add-line align-bottom me-1"></i> {{__('Add User')}}
                                    </button>
                                </div>
                            </div>
                        
                            <div class="row">
                                <div class="col-4 text-center">
                                </div>
                                <div class="col-4 text-center">
                                    @include('super-admins.pages.components.single-image',[$title = "Avatar"])

                                </div>
                                <div class="col-4 text-center">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="fName">{{__('First Name')}}</label>
                                        <input type="text" 
                                        class="form-control @error('fName') is-invalid @enderror
                                        @if(!$errors->has($fName) && !empty($fName)) is-valid @endif"
                                        wire:model="fName" placeholder="{{__('First Name')}}">
                                        @error('fName')
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                    <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="lName">{{__('Last Name')}}</label>
                                        <input type="text" 
                                        class="form-control @error('lName') is-invalid @enderror
                                        @if(!$errors->has($lName) && !empty($lName)) is-valid @endif"
                                        wire:model="lName" placeholder="{{__('Last Name')}}">
                                        @error('lName')
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                    <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="username">{{__('Username')}}</label>
                                        <input type="text" 
                                        class="form-control @error('username') is-invalid @enderror
                                        @if(!$errors->has($position) && !empty($position)) is-valid @endif"
                                        wire:model="username" placeholder="{{__('username')}}">
                                        @error('username')
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="position">{{__('Position')}}</label>
                                        <input type="text" 
                                        class="form-control @error('position') is-invalid @enderror
                                        @if(!$errors->has($position) && !empty($position)) is-valid @endif"
                                        wire:model="position" placeholder="{{__('Position')}}">
                                        @error('position')
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                    <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="phone">{{__('Phone')}}</label>
                                <input type="text" 
                                class="form-control @error('phone') is-invalid @enderror
                                @if(!$errors->has($phone) && !empty($phone)) is-valid @endif"
                                wire:model="phone" placeholder="{{__('phone')}}" 
                                oninput="this.value = this.value.replace(/[^0-9+]/g, '');">
                                @error('phone')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="email">{{__('Email Address')}}</label>
                                <input type="email" 
                                class="form-control @error('email') is-invalid @enderror
                                @if(!$errors->has($email) && !empty($email)) is-valid @endif"
                                wire:model="email" placeholder="{{__('Email Address')}}">
                                @error('email')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label for="password">{{__('Password')}}</label>
                                <input type="password" 
                                class="form-control @error('password') is-invalid @enderror
                                @if(!$errors->has($password) && !empty($password)) is-valid @endif"
                                wire:model="password" placeholder="{{__('Password')}}">
                                @error('password')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="status">{{__('Roles')}}</label>
                                @error('roles')
                                <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                    <span class="text-danger">{{ __($message) }}</span>
                                </div>
                                @enderror
                                <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" class="form-check-input" type="checkbox" value="1" id="role-admin">
                                                <label class="form-check-label" for="role-admin">Administrator</label>
                                                <p>Full control over the application, including user management, data management, and system configuration.</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" class="form-check-input" type="checkbox" value="2" id="role-data-entry">
                                                <label class="form-check-label" for="role-data-entry">Data Entry Specialist</label>
                                                <p>Responsible for entering and maintaining data within the application.</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" class="form-check-input" type="checkbox" value="3" id="role-finance-manager">
                                                <label class="form-check-label" for="role-finance-manager">Finance Manager</label>
                                                <p>Oversees financial operations, including budgeting, reporting, and analysis.</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" class="form-check-input" type="checkbox" value="4" id="role-order-processor">
                                                <label class="form-check-label" for="role-order-processor">Order Processor</label>
                                                <p>Processes customer orders, including order entry, verification, and fulfillment.</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" class="form-check-input" type="checkbox" value="5" id="role-create-action">
                                                <label class="form-check-label" for="role-create-action">Designer</label>
                                                <p>Responsible for entering Images data in the application.</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" class="form-check-input" type="checkbox" value="6" id="role-update-action">
                                                <label class="form-check-label" for="role-update-action">Update Action</label>
                                                <p>Responsible for maintaining data in the application.</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" class="form-check-input" type="checkbox" value="7" id="role-delete-action">
                                                <label class="form-check-label" for="role-delete-action">Delete Action</label>
                                                <p>Responsible for deleting existing data in the application.</p>
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="form-check">
                                                <input wire:model="roles" wire:click="driverCheck" class="form-check-input" type="checkbox" value="8" id="role-delete-action">
                                                <label class="form-check-label driver" for="role-delete-action">Driver</label>
                                                <p>Responsible for delivering products.</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if ($driverSection)
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="driverLic">{{__('Driving Lic. No.')}}</label>
                                        <input type="text" class="form-control" wire:model="driverLic" placeholder="1568091">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="plateNumber">{{__('Plate Number')}}</label>
                                        <input type="text" class="form-control" wire:model="plateNumber" placeholder="22 M 650124">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="vehicleLic">{{__('Vehicle Lic. No.')}}</label>
                                        <input type="text" class="form-control" wire:model="vehicleLic" placeholder="1503698">
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="mb-3">
                                        <label for="vehicleModel">{{__('Vehicle Model')}}</label>
                                        <input type="text" class="form-control" wire:model="vehicleModel" placeholder="Toyota">
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12">
                                    <div class="mb-3">
                                        <label for="vinNumber">{{__('Vehicle Vin No.')}}</label>
                                        <input type="text" class="form-control" wire:model="vinNumber" placeholder="4Y1SL65848Z411439">
                                    </div>
                                </div>
                            </div>
                            <hr>
                            @endif
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