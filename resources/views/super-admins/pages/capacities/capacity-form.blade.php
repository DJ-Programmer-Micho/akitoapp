<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="addCapacityModal" tabindex="-1" aria-labelledby="addCapacityModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="saveCapacity">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="addCapacityModalLabel">{{__('Add Capacity')}}</h5>
                            <button type="button" class="brn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="capacities.{{ $locale }}" class=" @if($locale != 'en') ar-shift @endif">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif
                                                @error('capacities.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('capacities.' . $locale) && !empty($capacities[$locale])) is-valid @endif"
                                                wire:model="capacities.{{ $locale }}" placeholder="{{__('Capacity Name')}}">
                                            @error('capacities.' . $locale)
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="filter-choices-input">
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
                                            class="form-control @error('status') is-invalid @enderror @if(!$errors->has('status') && $status) is-valid @endif" 
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
                                    
                                    <div class="mb-3">
                                        <label for="code">{{__('Code')}}</label>
                                        <input type="text" 
                                            class="form-control
                                            @error('code') is-invalid @enderror
                                            @if(!$errors->has('code') && !empty($code)) is-valid @endif"
                                            wire:model="code" placeholder="code">
                                        @error('code')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>    
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Add Capacity')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateCapacityModal" tabindex="-1" aria-labelledby="updateCapacityModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateCapacity">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateCapacityModalLabel">{{__('Edit Capacity')}}</h5>
                            <button type="button" class="brn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="capacitiesEdit.{{ $locale }}" class=" @if($locale != 'en') ar-shift @endif">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif
                                                @error('capacitiesEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('capacitiesEdit.' . $locale) && !empty($capacitiesEdit[$locale])) is-valid @endif"
                                                wire:model="capacitiesEdit.{{ $locale }}" placeholder="{{__('Capacity Name')}}">
                                            @error('capacitiesEdit.' . $locale)
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    <div class="mb-3">
                                        <label for="priorityEdit">{{__('Priority')}}</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('priorityEdit') is-invalid @enderror
                                            @if(!$errors->has('priorityEdit') && !empty($priorityEdit)) is-valid @endif"
                                            wire:model="priorityEdit" placeholder="priorityEdit">
                                        @error('priorityEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="statusEdit">{{__('Status')}}</label>
                                        <select 
                                            class="form-control @error('statusEdit') is-invalid @enderror @if(!$errors->has('statusEdit') && $statusEdit) is-valid @endif" 
                                            wire:model="statusEdit">
                                            <option value="">{{__('Select Status')}}</option>
                                            <option value="1" @if($statusEdit == 1) selected @endif>{{__('Active')}}</option>
                                            <option value="0" @if($statusEdit == 0) selected @endif>{{__('Non-Active')}}</option>
                                        </select>
                                        @error('statusEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
    
                                    <div class="mb-3">
                                        <label for="codeEdit">{{__('Code')}}</label>
                                        <input type="text" 
                                            class="form-control
                                            @error('codeEdit') is-invalid @enderror
                                            @if(!$errors->has('codeEdit') && !empty($codeEdit)) is-valid @endif"
                                            wire:model="codeEdit"  value="{{$codeEdit}}">
                                        @error('codeEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update Capacity')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteCapacityModal" tabindex="-1" aria-labelledby="deleteCapacityModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCapacityModalLabel">{{__('Delete Capacity')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroyCapacity">
                    <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                        <p>{{ __('Are you sure you want to delete this Capacity?') }}</p>
                        <p>{{ __('Please enter the in below to confirm:')}}</p>
                        <p>{{$showTextTemp}}</p>
                        <input type="text" wire:model="capacityNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || capacityNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>