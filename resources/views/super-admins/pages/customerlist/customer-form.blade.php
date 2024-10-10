<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="updateCustomerModal" tabindex="-1" aria-labelledby="updateCustomerModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateUser">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateCustomerModalLabel">{{__('Edit Customer')}}</h5>
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
                                                <label for="countryEdit">{{__('Country')}}</label>
                                                <input type="text" 
                                                class="form-control @error('countryEdit') is-invalid @enderror
                                                @if(!$errors->has($countryEdit) && !empty($countryEdit)) is-valid @endif"
                                                wire:model="countryEdit" placeholder="{{__('countryEdit')}}">
                                                @error('countryEdit')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                        <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="cityEdit">{{__('City')}}</label>
                                                <input type="text" 
                                                class="form-control @error('cityEdit') is-invalid @enderror
                                                @if(!$errors->has($cityEdit) && !empty($cityEdit)) is-valid @endif"
                                                wire:model="cityEdit" placeholder="{{__('cityEdit')}}">
                                                @error('cityEdit')
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
                                                <label for="addressEdit">{{__('Address')}}</label>
                                                <input type="text" 
                                                class="form-control @error('addressEdit') is-invalid @enderror
                                                @if(!$errors->has($addressEdit) && !empty($addressEdit)) is-valid @endif"
                                                wire:model="addressEdit" placeholder="{{__('addressEdit')}}">
                                                @error('addressEdit')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="zipcodeEdit">{{__('Zip Code')}}</label>
                                                <input type="text" 
                                                class="form-control @error('zipcodeEdit') is-invalid @enderror
                                                @if(!$errors->has($zipcodeEdit) && !empty($zipcodeEdit)) is-valid @endif"
                                                wire:model="zipcodeEdit" placeholder="{{__('zipcodeEdit')}}">
                                                @error('zipcodeEdit')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                        <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>

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