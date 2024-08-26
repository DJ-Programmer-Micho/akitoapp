<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="addMaterialModal" tabindex="-1" aria-labelledby="addMaterialModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="saveMaterial">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="addMaterialModalLabel">{{__('Add Material')}}</h5>
                            <button type="button" class="brn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="materials.{{ $locale }}">In {{ $locale }} Language</label>
                                            <input type="text" 
                                                class="form-control 
                                                @error('materials.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('materials.' . $locale) && !empty($materials[$locale])) is-valid @endif"
                                                wire:model="materials.{{ $locale }}" placeholder="{{__('Material Name')}}">
                                            @error('materials.' . $locale)
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
                                    <div class="mb-3">
                                        <label for="code">Code</label>
                                        <input type="text" 
                                            class="form-control
                                            @error('code') is-invalid @enderror
                                            @if(!$errors->has('code') && !empty($code)) is-valid @endif"
                                            wire:model="code" placeholder="code">
                                        @error('code')
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
                        <button type="submit" class="btn btn-primary submitJs">{{__('Add Material')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateMaterialModal" tabindex="-1" aria-labelledby="updateMaterialModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateMaterial">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateMaterialModalLabel">{{__('Edit Material')}}</h5>
                            <button type="button" class="brn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="materialsEdit.{{ $locale }}">In {{ $locale }} Language</label>
                                            <input type="text" 
                                                class="form-control 
                                                @error('materialsEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('materialsEdit.' . $locale) && !empty($materialsEdit[$locale])) is-valid @endif"
                                                wire:model="materialsEdit.{{ $locale }}" placeholder="{{__('Material Name')}}">
                                            @error('materialsEdit.' . $locale)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    <div class="mb-3">
                                        <label for="priorityEdit">Priority</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('priorityEdit') is-invalid @enderror
                                            @if(!$errors->has('priorityEdit') && !empty($priorityEdit)) is-valid @endif"
                                            wire:model="priorityEdit" placeholder="priorityEdit">
                                        @error('priorityEdit')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="statusEdit">Status</label>
                                        <select 
                                            class="form-control @error('statusEdit') is-invalid @enderror @if(!$errors->has('statusEdit') && $statusEdit) is-valid @endif" 
                                            wire:model="statusEdit"
                                        >
                                            <option value="">Select statusEdit</option>
                                            <option value="1" @if($statusEdit == 1) selected @endif>Active</option>
                                            <option value="0" @if($statusEdit == 0) selected @endif>Non-Active</option>
                                        </select>
                                        @error('statusEdit')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>

                                    <div class="mb-3">
                                        <label for="codeEdit">Code</label>
                                        <input type="text" 
                                            class="form-control
                                            @error('codeEdit') is-invalid @enderror
                                            @if(!$errors->has('codeEdit') && !empty($codeEdit)) is-valid @endif"
                                            wire:model="codeEdit"  value="{{$codeEdit}}">
                                        @error('codeEdit')
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
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update Material')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteMaterialModal" tabindex="-1" aria-labelledby="deleteMaterialModalLabel"
        aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteMaterialModalLabel">{{__('Delete Material')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroyMaterial">
                    <div class="modal-body">
                        <p>{{ __('Are you sure you want to delete this Material?') }}</p>
                        <p>{{ __('Please enter the')}}<strong> "{{$showTextTemp}}" </strong>{{__('to confirm:') }}</p>
                        <input type="text" wire:model="materialNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || materialNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div> 
</div>