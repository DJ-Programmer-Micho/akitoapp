<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="saveDiscount" tabindex="-1" aria-labelledby="saveDiscount" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="saveDiscount">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="saveDiscountModalLabel">{{__('Add New Discount')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-12">
                                <div class="filter-choices-input">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="customerSelect">{{__('Customer Select')}}</label>
                                                <select  class="form-control @error('selectedCustomer') is-invalid @enderror @if(!$errors->has('selectedCustomer') && $selectedCustomer !== null) is-valid @endif" wire:model="selectedCustomer">
                                                <option value="">{{__('Select Customer')}}</option>
                                                @forelse ($customers as $customer)
                                                    <option value="{{$customer->id}}">{{$customer->customer_profile->first_name . ' ' . $customer->customer_profile->last_name}}</option>
                                                @empty
                                                    
                                                @endforelse                                            </select>
                                                @error('selectedCustomer')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="customerSelect">{{__('Discount Type')}}</label>
                                                <select class="form-control @error('selectedType') is-invalid @enderror @if(!$errors->has('selectedType') && $selectedType !== null) is-valid @endif" 
                                                        wire:model="selectedType" 
                                                        wire:change="fetchData">
                                                    <option value="none">{{__('Discount Type')}}</option>
                                                    <option value="brand">{{__('Brands')}}</option>
                                                    <option value="category">{{__('Category')}}</option>
                                                    <option value="subcategory">{{__('Sub Category')}}</option>
                                                    <option value="product">{{__('Product')}}</option>
                                                </select>
                                                @error('selectedType')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                        <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="selectData">{{__('Select Data')}}</label>
                                                <select  class="form-control @error('selectedData') is-invalid @enderror @if(!$errors->has('selectedData') && $selectedData !== null) is-valid @endif" wire:model="selectedData">
                                                    <option value="">{{__('Select Data')}}</option>
                                                    @forelse ($fetchedData as $data)
                                                    <option value="{{$data->id}}">
                                                        @if($data->brandtranslation) 
                                                            {{$data->brandtranslation->name}} 
                                                        @elseif($data->categoryTranslation)
                                                            {{$data->categoryTranslation->name}}
                                                        @elseif($data->subCategoryTranslation)
                                                            {{$data->subCategoryTranslation->name}}
                                                        @elseif($data->productTranslation)
                                                            {{$data->productTranslation->first()->name}}
                                                        @endif
                                                    </option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                                @error('selectedData')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="percentageDiscount">{{__('Percentage %')}}</label>
                                                <input type="number" id="percentageDiscount" class="form-control @error('percentageDiscount') is-invalid @enderror @if(!$errors->has('percentageDiscount') && $percentageDiscount !== null) is-valid @endif" 
                                                wire:model="percentageDiscount" step="0.01" required>
                                                @error('percentageDiscount')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>     
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Submit')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateDiscount" tabindex="-1" aria-labelledby="updateDiscount" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateDiscount">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateDiscountModalLabel">{{__('Edit Discount')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal" aria-label="Close">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-12">
                                <div class="filter-choices-input">
                                    <div class="row">
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="customerSelect">{{__('Customer Select')}}</label>
                                                <select  class="form-control @error('selectedCustomer') is-invalid @enderror @if(!$errors->has('selectedCustomer') && $selectedCustomer !== null) is-valid @endif" wire:model="selectedCustomer">
                                                <option value="">{{__('Select Customer')}}</option>
                                                @forelse ($customers as $customer)
                                                    <option value="{{$customer->id}}">{{$customer->customer_profile->first_name . ' ' . $customer->customer_profile->last_name}}</option>
                                                @empty
                                                    
                                                @endforelse                                            </select>
                                                @error('selectedCustomer')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="customerSelect">{{__('Discount Type')}}</label>
                                                <select class="form-control @error('selectedType') is-invalid @enderror @if(!$errors->has('selectedType') && $selectedType !== null) is-valid @endif" 
                                                        wire:model="selectedType" 
                                                        wire:change="fetchData">
                                                    <option value="none">{{__('Discount Type')}}</option>
                                                    <option value="brand">{{__('Brands')}}</option>
                                                    <option value="category">{{__('Category')}}</option>
                                                    <option value="subcategory">{{__('Sub Category')}}</option>
                                                    <option value="product">{{__('Product')}}</option>
                                                </select>
                                                @error('selectedType')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                        <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="selectData">{{__('Select Data')}}</label>
                                                <select  class="form-control @error('selectedData') is-invalid @enderror @if(!$errors->has('selectedData') && $selectedData !== null) is-valid @endif" wire:model="selectedData">
                                                    <option value="">{{__('Select Data')}}</option>
                                                    @forelse ($fetchedData as $data)
                                                    <option value="{{$data->id}}">
                                                        @if($data->brandtranslation) 
                                                            {{$data->brandtranslation->name}} 
                                                        @elseif($data->categoryTranslation)
                                                            {{$data->categoryTranslation->name}}
                                                        @elseif($data->subCategoryTranslation)
                                                            {{$data->subCategoryTranslation->name}}
                                                        @elseif($data->productTranslation)
                                                            {{$data->productTranslation->first()->name}}
                                                        @endif
                                                    </option>
                                                    @empty
                                                        
                                                    @endforelse
                                                </select>
                                                @error('selectedData')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                        <div class="col-6">
                                            <div class="mb-3">
                                                <label for="percentageDiscount">{{__('Percentage %')}}</label>
                                                <input type="number" id="percentageDiscount" class="form-control @error('percentageDiscount') is-invalid @enderror @if(!$errors->has('percentageDiscount') && $percentageDiscount !== null) is-valid @endif" 
                                                wire:model="percentageDiscount" step="0.01" required>
                                                @error('percentageDiscount')
                                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                            <span class="text-danger">{{ __($message) }}</span>
                                                    </div>
                                                @enderror
                                            </div>
                                        </div>
                                    </div>
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

    <div wire:ignore.self class="modal fade" id="deleteDiscountModal" tabindex="-1" aria-labelledby="deleteDiscountModalLabel" aria-hidden="true">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteDiscountModalLabel">{{__('Delete')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroyDiscount">
                    <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                        <p>{{ __('Are you sure you want to delete this Customer Discount?') }}</p>
                        <p>{{ __('Please enter the in below to confirm:')}}</p>
                        <p>{{$showTextTemp}}</p>
                        <input type="text" wire:model="discountNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || discountNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>