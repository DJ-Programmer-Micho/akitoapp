<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="addSizeModal" tabindex="-1" aria-labelledby="addSizeModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="saveSize">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="addSizeModalLabel">{{__('Add Size')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i>
                            </button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="sizes.{{ $locale }}" class=" @if($locale != 'en') ar-shift @endif">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif 
                                                @error('sizes.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('sizes.' . $locale) && !empty($sizes[$locale])) is-valid @endif"
                                                wire:model="sizes.{{ $locale }}" placeholder="{{__('Size Name')}}">
                                            @error('sizes.' . $locale)
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
                        <button type="submit" class="btn btn-primary submitJs">{{__('Add Size')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateSizeModal" tabindex="-1" aria-labelledby="updateSizeModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateSize">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateSizeModalLabel">{{__('Edit Size')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="sizesEdit.{{ $locale }}" class=" @if($locale != 'en') ar-shift @endif">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif
                                                @error('sizesEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('sizesEdit.' . $locale) && !empty($sizesEdit[$locale])) is-valid @endif"
                                                wire:model="sizesEdit.{{ $locale }}" placeholder="{{__('Size Name')}}">
                                            @error('sizesEdit.' . $locale)
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
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update Size')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteSizeModal" tabindex="-1" aria-labelledby="deleteSizeModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSizeModalLabel">{{__('Delete Size')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroySize">
                    <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                        <p>{{ __('Are you sure you want to delete this Size?') }}</p>
                        <p>{{ __('Please enter the in below to confirm:')}}</p>
                        <p>{{$showTextTemp}}</p>
                        <input type="text" wire:model="sizeNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || sizeNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>