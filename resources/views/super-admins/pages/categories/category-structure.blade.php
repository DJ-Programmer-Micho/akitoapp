
<div class="page-content">
    <style>
        /* Ensure that dragging does not affect layout */
    .sortable-chosen {
        background: #f0f0f0;
        border: 1px solid #ccc;
    }
    
    /* Add styling for drag handles if necessary */
    .sortable-handle {
        cursor: move;
    }
    </style>
    <div class="container-fluid">
        @include('super-admins.pages.categories.category-form',[$title = "Category Image"])
        <div class="row p-0">
            <div class="col-lg-9">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <h5 class="text-center flex-grow-1">{{__('Category and Sub-Category Structure')}}</h5>
                        <button type="button" class="btn btn-primary" wire:click="reSyncA">
                            <i class="fas fa-sync-alt"></i>
                        </button>
                    </div>
                    <table class="table table-striped table-bordered mt-4 table-dark">
                        <thead>
                            <tr>
                                <th class="text-center">Image</th>
                                <th>Category</th>
                                <th class="text-center">Sub-Category</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="categories">
                            @forelse($categoriesData as $category)
                                <tr wire:key="category-{{ $category->id }}" data-id="{{ $category->id }}" class="category-row text-white">
                                    <td class="text-center">
                                        <img src="{{ app('cloudfront').$category->image }}" alt="{{$category->categoryTranslation->name}}" width="50" height="50" class="mx-auto" style="background-color: #f8d7da;">
                                    </td>
                                    <td>
                                        <strong>{{ $category->categoryTranslation->name }}</strong>
                                    </td>
                                    <td>
                                        <div id="sub-categories-{{ $category->id }}" class="sub-category-container">
                                            @forelse($category->subCategory as $subCategory)
                                                <div wire:key="sub-category-{{ $subCategory->id }}" data-id="{{ $subCategory->id }}" data-parent-id="{{ $category->id }}" class="sub-category-row border border-white rounded-3 p-2 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ $subCategory->subCategoryTranslation->name }}</span>
                                                        <div class="d-flex justify-content-end">
                                                            <button type="button" wire:click="editSubCategory({{ $subCategory->id }})" class="btn btn-primary btn-sm mx-1" data-bs-toggle="modal" data-bs-target="#updateSubCategoryModal" data-bs-placement="top" title="{{__('Edit Sub-Category')}}">
                                                                <i class="ri-pencil-fill"></i>
                                                            </button>
                                                            <button type="button" wire:click="deleteSubCategory({{ $subCategory->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteSubCategoryModal" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Sub-Category')}}">
                                                                <i class="fas fa-trash-alt"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center">No Sub-Categories Available</div>
                                            @endforelse
                                        </div>
                                    </td>
                                    
                                    <td class="text-center">
                                        <button type="button" wire:click="setSubCategory({{ $category->id }})" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#setSubCategoryModal" data-bs-placement="top" title="{{__('Add Sub-Category')}}">
                                            <i class="fas fa-plus"></i> 
                                        </button>
                                        <button type="button" wire:click="editCategory({{ $category->id }})" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#updateCategoryModal" data-bs-placement="top" title="{{__('Edit Category')}}">
                                            <i class="ri-pencil-fill"></i>
                                        </button>
                                        <button type="button" wire:click="deleteCategory({{ $category->id }})" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal" data-bs-placement="top" title="{{__('Delete Category')}}">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center">No Categories Available</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card p-4">
                    <h5 class="card-title mb-0 text-center mt-2">Input Field</h5>
                    <div class="mt-4">
                        <h5>Add Category</h5>
                        <div>
                            @foreach ($this->filteredLocales as $locale)
                            <div class="col mb-3">
                                <label for="names.{{$locale}}">In {{$locale}} Language</label>
                                <input type="text" class="form-control
                                @error('names.' . $locale) is-invalid @enderror
                                @if(!$errors->has('names.' . $locale) && !empty($names[$locale])) is-valid @endif"
                                wire:model="names.{{$locale}}" placeholder="{{__('Add Category')}}">
                                @error('names.' . $locale)
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-5 mx-auto">
                            @include('super-admins.pages.components.single-image',[$title = "Category Image"])
                        </div>
                        <button type="button" wire:click="addCategory" class="btn btn-primary add-btn w-100 mt-2">{{__('Add Category')}}</button>

                        {{-- @if ($selectedCategoryId)
                            @php
                                $selectedCategory = $categoriesData->firstWhere('id', $selectedCategoryId);
                                $selectedCategoryName = $selectedCategory->categoryTranslation->name ?? 'None';
                            @endphp
                            <h5 class="mt-5">Add Sub-Category to Selected Category: {{ $selectedCategoryName }}</h5>
                            <div class="row">
                                @foreach ($this->filteredLocales as $locale)
                                <div class="col-sm-4">
                                    <label for="subNames.{{$locale}}">In {{$locale}} Language</label>
                                    <input type="text" class="form-control
                                    @error('subNames.' . $locale) is-invalid @enderror
                                    @if(!$errors->has('subNames.' . $locale) && !empty($subNames[$locale])) is-valid @endif"
                                    wire:model.defer="subNames.{{$locale}}" placeholder="Add Sub-Category">
                                </div>
                                @endforeach
                            </div>
                            <button type="button" wire:click="addSubCategory({{ $selectedCategoryId }})" class="btn btn-primary add-btn w-100 mt-2">Add Sub-Category</button>
                        @else
                        <h5 class="mt-4">Select a category to add sub-categories.</h5>
                        @endif --}}
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>        
            
@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Sortable/1.14.0/Sortable.min.js"></script>
<script>
document.addEventListener('livewire:load', function () {
    function initializeSortable() {
        const categoriesList = document.getElementById('categories');

        if (categoriesList) {
            new Sortable(categoriesList, {
                handle: '.category-row',
                animation: 150,
                ghostClass: 'sortable-ghost',
                onStart: function(evt) {
                    const item = evt.item;
                    const parentId = item.getAttribute('data-id');
                    Array.from(categoriesList.querySelectorAll(`.sub-category-row[data-parent-id="${parentId}"]`)).forEach(el => {
                        el.style.display = 'none';
                    });
                },
                onEnd: function(evt) {
                    Array.from(categoriesList.querySelectorAll('.sub-category-row')).forEach(el => {
                        el.style.display = '';
                    });

                    const sortedCategories = Array.from(categoriesList.querySelectorAll('.category-row')).map((el, index) => ({
                        id: el.getAttribute('data-id'),
                        index: index
                    }));

                    Livewire.emit('updateCategoryOrder', sortedCategories);
                }
            });
        }
    }

    function initializeSubCategorySortable() {
        const categoriesData = @json($categoriesData);

        categoriesData.forEach(category => {
            const subCategoryList = document.getElementById(`sub-categories-${category.id}`);

            if (subCategoryList) {
                new Sortable(subCategoryList, {
                    group: 'shared',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    handle: '.sub-category-row',
                    onEnd: function(evt) {
                        const newParentId = evt.to.getAttribute('id').split('-')[2];
                        const subCategoryId = evt.item.getAttribute('data-id');
                        const oldParentId = evt.item.getAttribute('data-parent-id');

                        evt.item.setAttribute('data-parent-id', newParentId);

                        const sortedSubCategories = Array.from(evt.to.querySelectorAll('.sub-category-row')).map((el, index) => ({
                            id: el.getAttribute('data-id'),
                            parentId: el.getAttribute('data-parent-id'),
                            index: index
                        }));

                        Livewire.emit('updateSubCategoryOrder', sortedSubCategories, newParentId);
                    }
                });
            }
        });
    }

    Livewire.on('categoryAdded', initializeSortable);
    Livewire.on('subCategoryAdded', initializeSubCategorySortable);

    // Initializing both
    initializeSortable();
    initializeSubCategorySortable();
});
</script>
@endpush