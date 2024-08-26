<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="updateBrandModal" tabindex="-1" aria-labelledby="updateBrandModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateBrand">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateBrandModalLabel">{{__('Edit Brand')}}</h5>
                            <button type="button" class="brn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="brandsEdit.{{ $locale }}">In {{ $locale }} Language</label>
                                            <input type="text" 
                                                class="form-control 
                                                @error('brandsEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('brandsEdit.' . $locale) && !empty($brandsEdit[$locale])) is-valid @endif"
                                                wire:model="brandsEdit.{{ $locale }}" placeholder="Brand Name">
                                            @error('brandsEdit.' . $locale)
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    @endforeach
                                    <div class="mb-3">
                                        <label for="priority">Priority</label>
                                        <input type="text" 
                                            class="form-control 
                                            @error('priorityEdit') is-invalid @enderror
                                            @if(!$errors->has('priorityEdit') && !empty($priorityEdit)) is-valid @endif"
                                            wire:model="priorityEdit" placeholder="Priority">
                                        @error('priorityEdit')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="status">Status</label>
                                        <select 
                                            class="form-control @error('statusEdit') is-invalid @enderror @if(!$errors->has('statusEdit') && $status) is-valid @endif" 
                                            wire:model="statusEdit"
                                        >
                                            <option value="">Select Status</option>
                                            <option value="1" @if($statusEdit == 1) selected @endif>Active</option>
                                            <option value="0" @if($statusEdit == 0) selected @endif>Non-Active</option>
                                        </select>
                                        @error('statusEdit')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </div>
                            </div>
                            <div class="col-6">
                                @include('super-admins.pages.components.edit-single-image',[$title = "Brand Image"])
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

    <div wire:ignore.self class="modal fade" id="deleteBrandModal" tabindex="-1" aria-labelledby="deleteBrandModalLabel"
    aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog text-white">
        <div class="modal-content bg-dark">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteBrandModalLabel">{{__('Delete Menu')}}</h5>
                <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                    aria-label="Close"><i class="fas fa-times"></i></button>
            </div>
            <form wire:submit.prevent="destroyBrand">
                <div class="modal-body">
                    <p>{{ __('Are you sure you want to delete this Brand?') }}</p>
                    <p>{{ __('Please enter the')}}<strong> "{{$showTextTemp}}" </strong>{{__('to confirm:') }}</p>
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