<div class="page-content">
    @include('super-admins.pages.customerprofile.users-form',[$title = "Avatar"])
    <div class="container-fluid">
        <div class="profile-foreground position-relative mx-n4 mt-n4">
            <div class="profile-wid-bg">
                <img src="https://images.pexels.com/photos/428310/pexels-photo-428310.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1" alt="" class="profile-wid-img" />
            </div>
        </div>
        <div class="pt-4 mb-4 mb-lg-3 pb-lg-4 profile-wrapper">
            <div class="row g-4">
                <div class="col-auto">
                    <div class="avatar-lg">
                        <img src="{{$query->customer_profile->avatar ? app('cloudfront').$query->customer_profile->avatar : app('userImg')}}" alt="{{$query->customer_profile->first_name . ' ' . $query->customer_profile->last_name}}" 
                        class="img-thumbnail rounded-circle" 
                        style="width: 96px; height: 96px; object-fit: cover; border-radius: 50%; border: 3px solid white; object-position: center;"/>
                    </div>
                </div>
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{$query->customer_profile->first_name . ' ' . $query->customer_profile->last_name}}
                            @if ( $query->phone_verify == 1 && $query->phone_verify == 1 && $query->company_verify == 1 && $query->status == 1)
                            <span class="text-success" data-toggle="tooltip" data-placement="top" title="Verified">
                                <i class="fa-solid fa-circle"></i>
                            </span>
                            @elseif($query->phone_verify == 1 && $query->phone_verify == 1)
                            <span class="text-warning" data-toggle="tooltip" data-placement="top" title="Not Verified By Company">
                                <i class="fa-regular fa-circle-dot"></i>
                            </span>
                            @else
                            <span class="text-danger" data-toggle="tooltip" data-placement="top" title="Not Verified">
                                <i class="fa-regular fa-circle"></i>
                            </span>
                            @endif
                        </h3>
                        <p class="text-white text-opacity-75">Customer</p>
                    </div>
                </div>
                <!--end col-->
                <!--end col-->

            </div>
            <!--end row-->
        </div>

        <div class="row">
            <div class="col-lg-12">
                <div>
                    <div class="pt-4 text-muted">
                        <div class="row">
                            <div class="col-xxl-3">
                                <div class="card">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between">

                                            <h5 class="card-title mb-3">Customer Verification</h5>
                                            <div class="dropdown">
                                                <button
                                                    class="btn btn-soft-secondary btn-sm dropdown"
                                                    type="button" data-bs-toggle="dropdown"
                                                    aria-expanded="false"><i class="ri-more-fill"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end" style="">
                                                    <label class="mx-2 text-muted">Customer Status</label>
                                                    <li><button class="dropdown-item" type="button" wire:click="updateStatus({{ $query->id }})">
                                                        @if ( $query->status == 1)
                                                        <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('BLOCK')}}</span>
                                                        @else
                                                        <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Active')}}</span>
                                                        @endif
                                                        </button>
                                                    </li>
                                                    <li class="dropdown-divider"></li>
                                                    <label class="mx-2 text-muted">Company Status</label>
                                                    <li>
                                                        <button class="dropdown-item" type="button" wire:click="updateCompanyStatus({{ $query->id }})">
                                                        @if ( $query->company_verify == 1)
                                                        <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('Un-Verify')}}</span>
                                                        @else
                                                        <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Verify')}}</span>
                                                        @endif
                                                        </button>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>

                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Customer Status :</th>
                                                        <td>
                                                            @if ( $query->status == 1)
                                                            <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Active')}}</span>
                                                            @else
                                                            <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('Blocked')}}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Email Verification :</th>
                                                        <td>
                                                            @if ( $query->phone_verify == 1)
                                                            <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Verified')}}</span>
                                                            @else
                                                            <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('Not Verified')}}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Number Verification :</th>
                                                        <td>
                                                            @if ( $query->phone_verify == 1)
                                                            <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Verified')}}</span>
                                                            @else
                                                            <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('Not Verified')}}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Company Verification :</th>
                                                        <td>
                                                            @if ( $query->company_verify == 1)
                                                            <span class="text-success"><i class="fa-solid fa-check me-2"></i> {{__('Verified')}}</span>
                                                            @else
                                                            <span class="text-danger"><i class="fa-solid fa-xmark me-2"></i> {{__('Not Verified')}}</span>
                                                            @endif
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-3">Addition Information</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Full Name :</th>
                                                        <td class="text-muted">{{$query->customer_profile->first_name . ' ' . $query->customer_profile->last_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Mobile :</th>
                                                        <td class="text-muted">{{$query->customer_profile->phone_number}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">E-mail :</th>
                                                        <td class="text-muted">{{$query->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Joining Date</th>
                                                        <td class="text-muted">{{$query->created_at}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                                <!--end card-->
                                <div class="card">
                                    {{-- <div class="card-body">
                                        <h5 class="card-title mb-4">Roles</h5>
                                        <ul class="p-1">
                                            @foreach ($query->roles as $role)
                                            <li class="badge bg-primary-subtle text-primary" style="font-size: 0.90em">{{$role->name}}</li>
                                            @endforeach
                                        </ul>
                                    </div><!-- end card body --> --}}
                                </div><!-- end card -->
                            </div>
                            <!--end col-->
                            <div class="col-xxl-9 mt-5">
                                <div class="card mt-xxl-n5">
                                    <div class="card-header">
                                        <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                                            <li wire:ignore class="nav-item">
                                                <a class="nav-link text-body active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                                    <i class="fas fa-home"></i>
                                                    Personal Details
                                                </a>
                                            </li>
                                            <li  wire:ignore class="nav-item">
                                                <a class="nav-link text-body" data-bs-toggle="tab" href="#orderHistory" role="tab" wire:click="updateValidation">
                                                    <i class="far fa-user"></i>
                                                    Order History
                                                </a>
                                            </li>
                                            <li  wire:ignore class="nav-item">
                                                <a class="nav-link text-body" data-bs-toggle="tab" href="#changePassword" role="tab" wire:click="updateValidation">
                                                    <i class="far fa-user"></i>
                                                    Change Password
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                    <div class="card-body p-4">
                                        <div class="tab-content">
                                            <div  wire:ignore.self class="tab-pane active" id="personalDetails" role="tabpanel">
                                                    <div class="row">
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="firstnameInput" class="form-label">First Name</label>
                                                                <input type="text" class="form-control" value="{{$query->customer_profile->first_name}}" disabled>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="lastnameInput" class="form-label">Last Name</label>
                                                                <input type="text" class="form-control" value="{{$query->customer_profile->last_name}}" disabled>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="lastnameInput" class="form-label">Username</label>
                                                                <input type="text" class="form-control" value="{{$query->username}}" disabled>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="phonenumberInput" class="form-label">Phone Number</label>
                                                                <input type="text" class="form-control" value="{{$query->customer_profile->phone_number}}" disabled>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="emailInput" class="form-label">Email Address</label>
                                                                <input type="email" class="form-control" value="{{$query->email}}" disabled>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-12">
                                                            <div class="hstack gap-2 justify-content-end">
                                                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#updateUserModal" wire:click="editUser({{ $query->id }})">Edit?</button>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                    </div>
                                                    <!--end row-->
                                            </div>
                                            <!--end tab-pane-->
                                            <div  wire:ignore.self class="tab-pane" id="changePassword" role="tabpanel">
                                                <form wire:submit.prevent="updatePassword" action="javascript:void(0);">
                                                    <div class="row g-2">
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <label for="oldpasswordInput" class="form-label">Old Password*</label>
                                                                <input type="password" wire:model="old_password" class="form-control" id="oldpasswordInput" placeholder="Enter current password">
                                                                @error('old_password') <span class="text-danger">{{ $message }}</span> @enderror
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <label for="newpasswordInput" class="form-label">New Password*</label>
                                                                <input type="password" wire:model="new_password" class="form-control" id="newpasswordInput" placeholder="Enter new password">
                                                                @error('new_password') <span class="text-danger">{{ $message }}</span> @enderror
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-4">
                                                            <div>
                                                                <label for="confirmpasswordInput" class="form-label">Confirm Password*</label>
                                                                <input type="password" wire:model="new_password_confirmation" class="form-control" id="confirmpasswordInput" placeholder="Confirm password">
                                                                @error('new_password_confirmation') <span class="text-danger">{{ $message }}</span> @enderror
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-12">
                                                            <div class="mb-3">
                                                                <a href="javascript:void(0);" class="link-primary text-decoration-underline">Forgot Password?</a>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-12">
                                                            <div class="text-end">
                                                                <button type="submit" class="btn btn-success">Change Password</button>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                    </div>
                                                    <!--end row-->
                                                </form>
                                            </div>

                                            <div  wire:ignore.self class="tab-pane" id="orderHistory" role="tabpanel">
                                                <div class="mt-4 mb-3 border-bottom pb-2">
                                                    <div class="float-end">
                                                        <a href="javascript:void(0);" class="link-primary">View More Details</a>
                                                    </div>
                                                    <h5 class="card-title">All Orders</h5>
                                                </div>
                                                @foreach ($orderTable as $orderT)
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0 avatar-sm">
                                                        @if ($orderT->status == 'pending')
                                                        <div class="avatar-title bg-light text-warning rounded-3 fs-18">
                                                            <i class="fa-regular fa-hourglass-half"></i>
                                                        </div>
                                                        @elseif($orderT->status == 'shipping')
                                                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                                            <i class="fa-solid fa-truck-moving"></i>
                                                        </div>
                                                        @elseif($orderT->status == 'delivered')
                                                        <div class="avatar-title bg-light text-success rounded-3 fs-18">
                                                            <i class="fa-regular fa-circle-check"></i>
                                                        </div>
                                                        @elseif($orderT->status == 'canceled')
                                                        <div class="avatar-title bg-light text-danger rounded-3 fs-18">
                                                            <i class="fa-regular fa-circle-xmark"></i>
                                                        </div>
                                                        @else
                                                        <div class="avatar-title bg-light text-secondary rounded-3 fs-18">
                                                            <i class="fa-regular fa-face-frown"></i>
                                                        </div>
                                                            @endif
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6>{{$orderT->tracking_number}}</h6>
                                                        <p class="text-muted mb-0">{{$orderT->created_at}}</p>
                                                    </div>
                                                    <div>
                                                        <a href="{{ route('super.orderManagementsViewer', ['locale' => app()->getLocale(), 'id' => $orderT->id]) }}" target="_blank">View Details</a>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            <div>
                                                {{ $orderTable->links() }}
                                            </div>
                                            <!--end tab-pane-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <!--end tab-content-->
                    </div>
            <!--end col-->
                </div>
        <!--end row-->

            </div><!-- container-fluid -->
        </div><!-- End Page-content -->