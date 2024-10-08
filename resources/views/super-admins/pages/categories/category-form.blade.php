<div>
    <div wire:ignore.self class="modal fade overflow-auto" id="setSubCategoryModal" tabindex="-1" aria-labelledby="setSubCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="addSubCategory">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="setSubCategoryModalLabel">{{__('Add Sub-Category')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="subNames.{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif
                                                @error('subNames.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('subNames.' . $locale) && !empty($subNames[$locale])) is-valid @endif"
                                                wire:model="subNames.{{ $locale }}" placeholder="Sub Category Name">
                                            @error('subNames.' . $locale)
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
                                            @error('priorityEdit') is-invalid @enderror
                                            @if(!$errors->has('priorityEdit') && !empty($priorityEdit)) is-valid @endif"
                                            wire:model="priorityEdit" placeholder="Priority">
                                        @error('priorityEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="statusEdit">{{__('Status')}}</label>
                                        <select 
                                            class="form-control @error('statusEdit') is-invalid @enderror @if(!$errors->has('statusEdit') && $statusEdit != null) is-valid @endif" 
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
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade overflow-auto" id="updateCategoryModal" tabindex="-1" aria-labelledby="updateCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateCategory">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateCategoryModalLabel">{{__('Edit Category')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="namesEdit.{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif
                                                @error('namesEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('namesEdit.' . $locale) && !empty($namesEdit[$locale])) is-valid @endif"
                                                wire:model="namesEdit.{{ $locale }}" placeholder="Category Name">
                                            @error('namesEdit.' . $locale)
                                            <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                                <span class="text-danger">{{ __($message) }}</span>
                                            </div>
                                            @enderror
                                        </div>
                                    @endforeach
                                    <div class="mb-3">
                                        <label for="priority">{{__('Priority')}}</label>
                                        <input type="text" 
                                            class="form-control @if($locale != 'en') ar-shift @endif 
                                            @error('priorityEdit') is-invalid @enderror
                                            @if(!$errors->has('priorityEdit') && !empty($priorityEdit)) is-valid @endif"
                                            wire:model="priorityEdit" placeholder="Priority">
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
                            <div class="col-6">
                                <div class="filter-choices-input">
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
    
    <div wire:ignore.self class="modal fade overflow-auto" id="updateSubCategoryModal" tabindex="-1" aria-labelledby="updateSubCategoryModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl text-white mx-1 mx-lg-auto">
            <div class="modal-content bg-dark">
                <form wire:submit.prevent="updateSubCategory">
                    <div class="modal-body">
                        <div class="modal-header mb-3">
                            <h5 class="modal-title" id="updateSubCategoryModalLabel">{{__('Edit Sub-Category')}}</h5>
                            <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                                aria-label="Close"><i class="fas fa-times"></i></button>
                        </div>
                        <hr class="bg-white">
                        <div class="row">
                            <div class="col-6">
                                <div class="filter-choices-input">
                                    @foreach ($filteredLocales as $locale)
                                        <div class="mb-3">
                                            <label for="subNamesEdit.{{ $locale }}">{{__('In ' . $locale . ' Language')}}</label>
                                            <input type="text" 
                                                class="form-control @if($locale != 'en') ar-shift @endif
                                                @error('subNamesEdit.' . $locale) is-invalid @enderror
                                                @if(!$errors->has('subNamesEdit.' . $locale) && !empty($subNamesEdit[$locale])) is-valid @endif"
                                                wire:model="subNamesEdit.{{ $locale }}" placeholder="Category Name">
                                            @error('subNamesEdit.' . $locale)
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
                                            @error('priorityEdit') is-invalid @enderror
                                            @if(!$errors->has('priorityEdit') && !empty($priorityEdit)) is-valid @endif"
                                            wire:model="priorityEdit" placeholder="Priority">
                                        @error('priorityEdit')
                                        <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                            <span class="text-danger">{{ __($message) }}</span>
                                        </div>
                                        @enderror
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="statusEdit">{{__('Status')}}</label>
                                        <select 
                                            class="form-control @error('statusEdit') is-invalid @enderror @if(!$errors->has('statusEdit') && $statusEdit != null) is-valid @endif" 
                                            wire:model="statusEdit">
                                            <option value="">Select Status</option>
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
                        <button type="submit" class="btn btn-primary submitJs">{{__('Update')}}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteCategoryModalLabel">{{__('Delete Category')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroyCategory">
                    <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                        <p>{{ __('Are you sure you want to delete this Category?') }}</p>
                        <p>{{ __('Please enter the in below to confirm:')}}</p>
                        <p>{{$showTextTemp}}</p>
                        <input type="text" wire:model="categoryNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || categoryNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div wire:ignore.self class="modal fade" id="deleteSubCategoryModal" tabindex="-1" aria-labelledby="deleteSubCategoryModalLabel"
        aria-hidden="true">
        <div class="modal-dialog text-white">
            <div class="modal-content bg-dark">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteSubCategoryModalLabel">{{__('Delete Sub-Category')}}</h5>
                    <button type="button" class="btn btn-danger" data-dismiss="modal" wire:click="closeModal"
                        aria-label="Close"><i class="fas fa-times"></i></button>
                </div>
                <form wire:submit.prevent="destroySubCategory">
                    <div class="modal-body @if(app()->getLocale() != 'en') ar-shift @endif">
                        <p>{{ __('Are you sure you want to delete this Sub-Category?') }}</p>
                        <p>{{ __('Please enter the in below to confirm:')}}</p>
                        <p>{{$showTextTemp}}</p>
                        <input type="text" wire:model="subCategoryNameToDelete" class="form-control">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeModal"
                            data-dismiss="modal">{{__('Cancel')}}</button>
                            <button type="submit" class="btn btn-danger" wire:disabled="!confirmDelete || subCategoryNameToDelete !== $showTextTemp">
                                {{ __('Yes! Delete') }}
                            </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
</div>