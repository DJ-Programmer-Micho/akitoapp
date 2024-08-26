<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="addTagModal" tabindex="-1" aria-labelledby="addTagModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="saveTag">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="addTagModalLabel">{{__('Add Tag')}}</h5>
                            <button type="button" class="brn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="tags.{{ $locale }}">In {{ $locale }} Language</label>
                                            <input type="text" 
                                                class="form-control 
                                                @error('tags.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('tags.' . $locale) && !empty($tags[$locale])) is-valid @endif"
                                                wire:model="tags.{{ $locale }}" placeholder="{{__('Tag Name')}}">
                                            @error('tags.' . $locale)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    <div class="mb-3">
                                        <label for="priority">Priority</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('priority') is-invalid @enderror
                                            @if(!$errors->has('priority') && !empty($priority)) is-valid @endif"
                                            wire:model="priority" placeholder="Priority">
                                        @error('priority')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <select 
                                            class="form-control @error('status') is-invalid @enderror @if(!$errors->has('status') && $status) is-valid @endif" 
                                            wire:model="status"
                                        >
                                            <option value="">Select Status</option>
                                            <option value="1" @if($status == 1) selected @endif>Active</option>
                                            <option value="0" @if($status == 0) selected @endif>Non-Active</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>                        
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Add Tag')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateTagModal" tabindex="-1" aria-labelledby="updateTagModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateTag">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateTagModalLabel">{{__('Update Tag')}}</h5>
                            <button type="button" class="brn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="tagsEdit.{{ $locale }}">In {{ $locale }} Language</label>
                                            <input type="text" 
                                                class="form-control 
                                                @error('tagsEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('tagsEdit.' . $locale) && !empty($tagsEdit[$locale])) is-valid @endif"
                                                wire:model="tagsEdit.{{ $locale }}" placeholder="{{__('Tag Name')}}">
                                            @error('tagsEdit.' . $locale)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    <div class="mb-3">
                                        <label for="priority">Priority</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('priority') is-invalid @enderror
                                            @if(!$errors->has('priority') && !empty($priority)) is-valid @endif"
                                            wire:model="priority" placeholder="Priority">
                                        @error('priority')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <select 
                                            class="form-control @error('status') is-invalid @enderror @if(!$errors->has('status') && $status) is-valid @endif" 
                                            wire:model="status"
                                        >
                                            <option value="">Select Status</option>
                                            <option value="1" @if($status == 1) selected @endif>Active</option>
                                            <option value="0" @if($status == 0) selected @endif>Non-Active</option>
                                        </select>
                                        @error('status')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Close')}}</button>
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update Tag')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteTagModal" tabindex="-1" aria-labelledby="deleteTagModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteTagModalLabel">{{__('Delete Tag')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroyTag">
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to delete this Tag?') }}</p>
                        <p>{{ __('Please enter the')}}<strong> "{{$showTextTemp}}" </strong>{{__('to confirm:') }}</p>
                        <input type="text" wire:model="tagNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || tagNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>