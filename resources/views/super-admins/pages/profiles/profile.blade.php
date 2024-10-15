<div class="page-content">
    @include('super-admins.pages.profiles.users-form',[$title = "Avatar"])
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
                        <img src="{{isset(auth()->guard('admin')->user()->profile->avatar) ? app('cloudfront').auth()->guard('admin')->user()->profile->avatar : app('userImg')}}" alt="{{auth()->guard('admin')->user()->profile->first_name . ' ' . auth()->guard('admin')->user()->profile->last_name}}" class="img-thumbnail rounded-circle" />
                    </div>
                </div>
                <!--end col-->
                <div class="col">
                    <div class="p-2">
                        <h3 class="text-white mb-1">{{auth()->guard('admin')->user()->profile->first_name . ' ' . auth()->guard('admin')->user()->profile->last_name}}</h3>
                        <p class="text-white text-opacity-75">{{auth()->guard('admin')->user()->profile->position}}</p>
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
                                        <h5 class="card-title mb-3">Info</h5>
                                        <div class="table-responsive">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Full Name :</th>
                                                        <td class="text-muted">{{auth()->guard('admin')->user()->profile->first_name . ' ' . auth()->guard('admin')->user()->profile->last_name}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Mobile :</th>
                                                        <td class="text-muted">{{auth()->guard('admin')->user()->profile->phone_number}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">E-mail :</th>
                                                        <td class="text-muted">{{auth()->guard('admin')->user()->email}}</td>
                                                    </tr>
                                                    <tr>
                                                        <th class="ps-0" scope="row">Joining Date</th>
                                                        <td class="text-muted">{{auth()->guard('admin')->user()->created_at}}</td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div><!-- end card body -->
                                </div><!-- end card -->
                                <!--end card-->
                                <div class="card">
                                    <div class="card-body">
                                        <h5 class="card-title mb-4">Roles</h5>
                                        <ul class="p-1">
                                            @foreach ($query->roles as $role)
                                            <li class="badge bg-primary-subtle text-primary" style="font-size: 0.90em">{{$role->name}}</li>
                                            @endforeach
                                        </ul>
                                    </div><!-- end card body -->
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
                                                                <input type="text" class="form-control" value="{{$query->profile->first_name}}" disabled>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="lastnameInput" class="form-label">Last Name</label>
                                                                <input type="text" class="form-control" value="{{$query->profile->last_name}}" disabled>
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
                                                                <label for="lastnameInput" class="form-label">Position</label>
                                                                <input type="text" class="form-control" value="{{$query->profile->position}}" disabled>
                                                            </div>
                                                        </div>
                                                        <!--end col-->
                                                        <div class="col-lg-6">
                                                            <div class="mb-3">
                                                                <label for="phonenumberInput" class="form-label">Phone Number</label>
                                                                <input type="text" class="form-control" value="{{$query->profile->phone_number}}" disabled>
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
                                                
                                                {{-- <div class="mt-4 mb-3 border-bottom pb-2">
                                                    <div class="float-end">
                                                        <a href="javascript:void(0);" class="link-primary">All Logout</a>
                                                    </div>
                                                    <h5 class="card-title">Login History</h5>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0 avatar-sm">
                                                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                                            <i class="ri-smartphone-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6>iPhone 12 Pro</h6>
                                                        <p class="text-muted mb-0">Los Angeles, United States - March 16 at
                                                            2:47PM</p>
                                                    </div>
                                                    <div>
                                                        <a href="javascript:void(0);">Logout</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0 avatar-sm">
                                                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                                            <i class="ri-tablet-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6>Apple iPad Pro</h6>
                                                        <p class="text-muted mb-0">Washington, United States - November 06
                                                            at 10:43AM</p>
                                                    </div>
                                                    <div>
                                                        <a href="javascript:void(0);">Logout</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center mb-3">
                                                    <div class="flex-shrink-0 avatar-sm">
                                                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                                            <i class="ri-smartphone-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6>Galaxy S21 Ultra 5G</h6>
                                                        <p class="text-muted mb-0">Conneticut, United States - June 12 at
                                                            3:24PM</p>
                                                    </div>
                                                    <div>
                                                        <a href="javascript:void(0);">Logout</a>
                                                    </div>
                                                </div>
                                                <div class="d-flex align-items-center">
                                                    <div class="flex-shrink-0 avatar-sm">
                                                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                                            <i class="ri-macbook-line"></i>
                                                        </div>
                                                    </div>
                                                    <div class="flex-grow-1 ms-3">
                                                        <h6>Dell Inspiron 14</h6>
                                                        <p class="text-muted mb-0">Phoenix, United States - July 26 at
                                                            8:10AM</p>
                                                    </div>
                                                    <div>
                                                        <a href="javascript:void(0);">Logout</a>
                                                    </div>
                                                </div> --}}
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