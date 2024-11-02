{{-- file path: resources/views/super-admins/pages/brands/brand-form.blade.php --}}
<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="updateUserModal" tabindex="-1" aria-labelledby="updateUserModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateUser">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateUserModalLabel">{{__('Edit User')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-12 col-md-6">
                                <div class="filter-choices-input">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="fNameEdit">{{__('First Name')}}</label>
                                                <input type="text" 
                                                class="form-control @error('fNameEdit') is-invalid @enderror
                                                @if(!$errors->has($fNameEdit) && !empty($fNameEdit)) is-valid @endif"
                                                wire:model="fNameEdit" placeholder="{{__('First Name')}}">
                                                @error('fNameEdit')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="lNameEdit">{{__('Last Name')}}</label>
                                                <input type="text" 
                                                class="form-control @error('lNameEdit') is-invalid @enderror
                                                @if(!$errors->has($lNameEdit) && !empty($lNameEdit)) is-valid @endif"
                                                wire:model="lNameEdit" placeholder="{{__('Last Name')}}">
                                                @error('lNameEdit')
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
                                                <label for="usernameEdit">{{__('Username')}}</label>
                                                <input type="text" 
                                                class="form-control @error('usernameEdit') is-invalid @enderror
                                                @if(!$errors->has($usernameEdit) && !empty($usernameEdit)) is-valid @endif"
                                                wire:model="usernameEdit" placeholder="{{__('usernameEdit')}}">
                                                @error('usernameEdit')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                        <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="positionEdit">{{__('Position')}}</label>
                                                <input type="text" 
                                                class="form-control @error('positionEdit') is-invalid @enderror
                                                @if(!$errors->has($positionEdit) && !empty($positionEdit)) is-valid @endif"
                                                wire:model="positionEdit" placeholder="{{__('positionEdit')}}">
                                                @error('positionEdit')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label for="rolesEdit">{{__('Roles')}}</label>
                                        @error('rolesEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                        <div class="d-flex flex-column gap-2 mt-3 filter-check">
                                            <div class="row">
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" class="form-check-input" type="checkbox" value="1" id="role-admin">
                                                        <label class="form-check-label" for="role-admin">Administrator</label>
                                                        <p>Full control over the application, including user management, data management, and system configuration.</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" class="form-check-input" type="checkbox" value="2" id="role-data-entry">
                                                        <label class="form-check-label" for="role-data-entry">Data Entry Specialist</label>
                                                        <p>Responsible for entering and maintaining data within the application.</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" class="form-check-input" type="checkbox" value="3" id="role-finance-manager">
                                                        <label class="form-check-label" for="role-finance-manager">Finance Manager</label>
                                                        <p>Oversees financial operations, including budgeting, reporting, and analysis.</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" class="form-check-input" type="checkbox" value="4" id="role-order-processor">
                                                        <label class="form-check-label" for="role-order-processor">Order Processor</label>
                                                        <p>Processes customer orders, including order entry, verification, and fulfillment.</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" class="form-check-input" type="checkbox" value="5" id="role-create-action">
                                                        <label class="form-check-label" for="role-create-action">Designer</label>
                                                        <p>Responsible for Images data in the application.</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" class="form-check-input" type="checkbox" value="6" id="role-update-action">
                                                        <label class="form-check-label" for="role-update-action">Update Action</label>
                                                        <p>Responsible for maintaining data in the application.</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" class="form-check-input" type="checkbox" value="7" id="role-delete-action">
                                                        <label class="form-check-label" for="role-delete-action">Delete Action</label>
                                                        <p>Responsible for deleting existing data in the application.</p>
                                                    </div>
                                                </div>
                                                <div class="col-6">
                                                    <div class="form-check">
                                                        <input wire:model="rolesEdit" wire:click="driverCheck" class="form-check-input" type="checkbox" value="8" id="role-delete-action">
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
                                                <label for="driverLicEdit">{{__('Driving Lic. No.')}}</label>
                                                <input type="text" class="form-control" wire:model="driverLicEdit" placeholder="1568091">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="plateNumberEdit">{{__('Plate Number')}}</label>
                                                <input type="text" class="form-control" wire:model="plateNumberEdit" placeholder="22 M 650124">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="vehicleLicEdit">{{__('Vehicle Lic. No.')}}</label>
                                                <input type="text" class="form-control" wire:model="vehicleLicEdit" placeholder="1503698">
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="vehicleModelEdit">{{__('Vehicle Model')}}</label>
                                                <input type="text" class="form-control" wire:model="vehicleModelEdit" placeholder="Toyota">
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-12">
                                            <div class="mb-3">
                                                <label for="vinNumberEdit">{{__('Vehicle Vin No.')}}</label>
                                                <input type="text" class="form-control" wire:model="vinNumberEdit" placeholder="4Y1SL65848Z411439">
                                            </div>
                                        </div>
                                    </div>
                                    <hr>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-6">
                                <div class="mb-3">
                                    <label for="phoneEdit">{{__('Phone')}}</label>
                                    <input type="text" 
                                    class="form-control @error('phoneEdit') is-invalid @enderror
                                    @if(!$errors->has($phoneEdit) && !empty($phoneEdit)) is-valid @endif"
                                    wire:model="phoneEdit" placeholder="{{__('phoneEdit')}}" 
                                    oninput="this.value = this.value.replace(/[^0-9+]/g, '');">
                                    @error('phoneEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                    @enderror
                                </div>
    
                                <div class="mb-3">
                                    <label for="emailEdit">{{__('Email Address')}}</label>
                                    <input type="email" 
                                    class="form-control @error('emailEdit') is-invalid @enderror
                                    @if(!$errors->has($emailEdit) && !empty($emailEdit)) is-valid @endif"
                                    wire:model="emailEdit" placeholder="{{__('Email Address')}}">
                                    @error('emailEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                    @enderror
                                </div>
    
                                {{-- <div class="mb-3">
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
                                </div> --}}

                                <div class="mb-3">
                                    <label for="statusEdit">{{__('Status')}}</label>
                                    <select 
                                    class="form-control @error('statusEdit') is-invalid @enderror @if(!$errors->has('statusEdit') && $statusEdit !== null) is-valid @endif" 
                                    wire:model="statusEdit">
                                    <option value="">{{__('Select Status')}}</option>
                                    <option value="1" @if($statusEdit == 1) selected @endif>{{__('Active')}}</option>
                                    <option value="0" @if($statusEdit == 0) selected @endif>{{__('Non-Active')}}</option>
                                </select>
                                
                                @error('statusEdit')
                                <span class="text-danger">{{ __($message) }}</span>
                                @enderror
                                </div>

                                <div class="col-12 col-md-6 mx-auto">
                                    @include('super-admins.pages.components.edit-single-image',[$title = "Upload Image"])
                                </div>
                            </div>
                        </div>     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteUserModal" tabindex="-1" aria-labelledby="deleteUserModalLabel" aria-hidden="true">
    <div class="modal-dialog text-white">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteUserModalLabel">{{__('Delete')}}</h5>
                <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                    aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <form wire:submit.prevent="destroyBrand">
                <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                    <p>{{ __('Are you sure you want to delete this User?') }}</p>
                    <p>{{ __('Please enter the in below to confirm:')}}</p>
                    <p>{{$showTextTemp}}</p>
                    <input type="text" wire:model="brandNameToDelete" class="form-control">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeModal"
                        data-dismiss="modal">{{__('Cancel')}}</button>
                        <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || brandNameToDelete !== $showTextTemp">
                            {{ __('Yes! Delete') }}
                        </button>
                </div>
            </form>
        </div>
    </div>
</div>
</div>