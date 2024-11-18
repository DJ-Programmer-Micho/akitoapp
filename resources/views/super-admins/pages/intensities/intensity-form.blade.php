<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="addIntensityModal" tabindex="-1" aria-labelledby="addIntensityModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="saveIntensity">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="addIntensityModalLabel">{{__('Add Intensity')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i>
                            </button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="mb-3">
                                    <label for="priority">{{__('Min Value')}}</label>
                                    <input type="number" 
                                        class="form-control 
                                        @error('minVal') is-invalid @enderror
                                        @if(!$errors->has('minVal') && !empty($minVal)) is-valid @endif"
                                        wire:model="minVal" placeholder="minVal">
                                    @error('minVal')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                        <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label for="priority">{{__('Max Value')}}</label>
                                    <input type="number" 
                                        class="form-control 
                                        @error('maxVal') is-invalid @enderror
                                        @if(!$errors->has('maxVal') && !empty($maxVal)) is-valid @endif"
                                        wire:model="maxVal" placeholder="maxVal">
                                    @error('maxVal')
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                        <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                                    @enderror
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Add Intensity')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateIntensityModal" tabindex="-1" aria-labelledby="updateIntensityModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateIntensity">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateIntensityModalLabel">{{__('Edit Intensity')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    <div class="mb-3">
                                        <label for="minValEdit">{{__('Min Value')}}</label>
                                        <input type="number" 
                                            class="form-control 
                                            @error('minValEdit') is-invalid @enderror
                                            @if(!$errors->has('minValEdit') && !empty($minValEdit)) is-valid @endif"
                                            wire:model="minValEdit" placeholder="Min Value">
                                        @error('minValEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label for="maxValEdit">{{__('Max Value')}}</label>
                                        <input type="number" 
                                            class="form-control 
                                            @error('maxValEdit') is-invalid @enderror
                                            @if(!$errors->has('maxValEdit') && !empty($maxValEdit)) is-valid @endif"
                                            wire:model="maxValEdit" placeholder="Max Value">
                                        @error('maxValEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    <div class="mb-3">
                                        <label for="priorityEdit">{{__('Priority')}}</label>
                                        <input type="number" 
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
                                </div>
                            </div>
                        </div> 
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update Intensity')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteIntensityModal" tabindex="-1" aria-labelledby="deleteIntensityModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteIntensityModalLabel">{{__('Delete Intensity')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroyIntensity">
                    <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                        <p>{{ __('Are you sure you want to delete this Intensity?') }}</p>
                        <p>{{ __('Please enter the in below to confirm:')}}</p>
                        <p>{{$showTextTemp}}</p>
                        <input type="text" wire:model="intensityNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || intensityNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>