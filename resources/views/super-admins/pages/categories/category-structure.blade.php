
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
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Categories and Sub-Categories')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Categories')}}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>

        @include('super-admins.pages.categories.category-form',[$title = "Upload Image"])
        <div class="row p-0">
            <div class="col-lg-9">
                <div class="card p-4">
                    <div class="d-flex justify-content-between align-items-center mt-2">
                        <h5 class="text-center flex-grow-1">{{__('Category and Sub-Category Structure')}}</h5>
                        {{-- <button type="button" class="btn btn-primary" wire:click="reSyncA">
                            <i class="fas fa-sync-alt"></i>
                        </button> --}}
                    </div>
                    <div class="table-responsive">
                    <table class="table table-striped table-bordered mt-4 table-dark">
                        <thead>
                            <tr>
                                <th class="text-center">{{__('Image')}}</th>
                                <th class="text-center">{{__('Category')}}</th>
                                <th class="text-center">{{__('Sub-Category')}}</th>
                                <th class="text-center">{{__('Status')}}</th>
                                <th class="text-center">{{__('Actions')}}</th>
                            </tr>
                        </thead>
                        <tbody id="categories">
                            @forelse($categoriesData as $category)
                                <tr wire:key="category-{{ $category->id }}" data-id="{{ $category->id }}" class="category-row text-white">
                                    <td class="align-middle text-center">
                                        <img src="{{ app('cloudfront').$category->image }}" alt="{{$category->categoryTranslation->name}}" width="50" height="50" class="mx-auto" style="background-color: #f8d7da;">
                                    </td>
                                    <td class="align-middle text-center">
                                        <strong>{{ $category->categoryTranslation->name }}</strong>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div id="sub-categories-{{ $category->id }}" class="sub-category-container">
                                            @forelse($category->subCategory as $subCategory)
                                                <div wire:key="sub-category-{{ $subCategory->id }}" data-id="{{ $subCategory->id }}" data-parent-id="{{ $category->id }}" class="sub-category-row border border-white rounded-3 p-2 mb-2">
                                                    <div class="d-flex justify-content-between align-items-center">
                                                        <span>{{ $subCategory->subCategoryTranslation->name }}</span>
                                                        <div class="d-flex justify-content-end">

                                                            <button type="button" wire:click="updateSubCatStatus({{ $subCategory->id }})" class="btn {{ $subCategory->status == 0 ? 'btn-danger' : 'btn-success' }} btn-icon m-1" data-bs-placement="top" title="{{ $subCategory->status == 0 ? __('De-Active') : __('Active') }}">
                                                                <i class="far {{ $subCategory->status == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                                            </button>
                                                            <button type="button" wire:click="editSubCategory({{ $subCategory->id }})" class="btn btn-primary btn-icon m-1" data-bs-toggle="modal" data-bs-target="#updateSubCategoryModal" data-bs-placement="top" title="{{__('Edit Sub-Category')}}">
                                                                <i class="fa-regular fa-pen-to-square"></i>
                                                            </button>
                                                            <button type="button" wire:click="deleteSubCategory({{ $subCategory->id }})" class="btn btn-danger btn-icon m-1" data-bs-toggle="modal" data-bs-target="#deleteSubCategoryModal" data-bs-toggle="tooltip" data-bs-placement="top" title="{{__('Delete Sub-Category')}}">
                                                                <i class="fa-regular fa-trash-can"></i>
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            @empty
                                                <div class="text-center">{{__('No Sub-Categories Available')}}</div>
                                            @endforelse
                                        </div>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="badge {{ $category->status ? 'bg-success' : 'bg-danger' }} p-2" style="font-size: 0.7rem;">
                                            {{ $category->status ? 'Active' : 'Non-Active' }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <button type="button" wire:click="updateCatStatus({{ $category->id }})" class="btn {{ $category->status == 0 ? 'btn-danger' : 'btn-success' }} btn-icon m-1" data-bs-placement="top" title="{{ $category->status == 0 ? __('De-Active') : __('Active') }}">
                                            <i class="far {{ $category->status == 0 ? 'fa-times-circle' : 'fa-check-circle' }}"></i>
                                        </button>
                                        <button type="button" wire:click="setSubCategory({{ $category->id }})" class="btn btn-success btn-icon m-1" data-bs-toggle="modal" data-bs-target="#setSubCategoryModal" data-bs-placement="top" title="{{__('Add Sub-Category')}}">
                                            <i class="fa-solid fa-plus"></i>
                                        </button>
                                        <button type="button" wire:click="editCategory({{ $category->id }})" class="btn btn-primary btn-icon m-1" data-bs-toggle="modal" data-bs-target="#updateCategoryModal" data-bs-placement="top" title="{{__('Edit Category')}}">
                                            <i class="fa-regular fa-pen-to-square"></i>
                                        </button>
                                        <button type="button" wire:click="deleteCategory({{ $category->id }})" class="btn btn-danger btn-icon m-1" data-bs-toggle="modal" data-bs-target="#deleteCategoryModal" data-bs-placement="top" title="{{__('Delete Category')}}">
                                            <i class="fa-regular fa-trash-can"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr class="align-middle text-center">
                                    <td colspan="5" class="text-center">{{__('No Categories Available')}}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="card p-4">
                    <div class="mt-4">
                        <h5>{{__('Add Category')}}</h5>
                        <div>
                            @foreach ($this->filteredLocales as $locale)
                            <div class="col mb-3">
                                <label for="names.{{$locale}}">{{__('In ' . $locale . ' Language')}}</label>
                                <input type="text" class="form-control @if($locale != 'en') ar-shift @endif
                                @error('names.' . $locale) is-invalid @enderror
                                @if(!$errors->has('names.' . $locale) && !empty($names[$locale])) is-valid @endif"
                                wire:model="names.{{$locale}}" placeholder="{{__('Add Category')}}">
                                @error('names.' . $locale)
                                    <div class="@if(app()->getLocale() != 'en') ar-shift @endif">
                                        <span class="text-danger">{{ __($message) }}</span>
                                    </div>
                            @enderror
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-5 mx-auto">
                            @include('super-admins.pages.components.single-image',[$title = "Upload Image"])
                        </div>
                        <button type="button" wire:click="addCategory" class="btn btn-primary add-btn w-100 mt-2">{{__('Add Category')}}</button>
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