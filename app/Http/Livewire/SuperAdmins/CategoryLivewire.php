<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use App\Models\CategoryTranslation;
use App\Models\SubCategoryTranslation;
use Illuminate\Support\Facades\Storage;

class CategoryLivewire extends Component
{
    // INT
    public $glang;
    public $filteredLocales;
    public $names = [];
    public $subNames = [];
    public $status = 1;
    public $selectedCategoryId;
    // EDIT
    public $namesEdit = [];
    public $slugEdit = [];
    public $priorityEdit;
    public $statusEdit;
    public $category_update;
    public $sub_category_update;
    public $subNamesEdit = [];
    // DELETE
    public $category_selected_id_delete;
    public $category_name_selected_delete;
    public $subCategory_selected_id_delete;
    public $subCategory_name_selected_delete;
    public $showTextTemp;
    public $confirmDelete;
    // Temp
    public $de = 1;
    public $subaddid;
    public $objectReader;
    public $objectName;
    public $objectData;
    // VALIDATION
    public $currentValidation = 'categoryAdd';
    //LISTENERS
    protected $listeners = [
        'imgCrop' => 'handleCroppedImage',
        'updateCategoryOrder' => 'updateCategoryOrderA',
        'updateSubCategoryOrder' => 'updateSubCategoryOrderA',
    ];
    // ON LOAD
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
    }

    // VALIDATION
    protected function rules()
    {
        //Keep It Empty
    }

    protected function rulesForCategory()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['names.' . $locale] = [
                'required',
                'string',
                'min:2',
                Rule::unique('category_translations', 'name')
                    ->where('locale', $locale)
            ];
        }
        return $rules;
    }
    
    protected function rulesForCategoryEdit()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['namesEdit.' . $locale] = [
                'required',
                'string',
                'min:2',
                Rule::unique('category_translations', 'name')
                    ->where('locale', $locale)
                    ->ignore($this->category_update->id, 'category_id')
            ];
    
            // Add custom uniqueness check across locales
            $rules['namesEdit.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $this->namesEdit[$locale] === $this->namesEdit[$otherLocale]) {
                        $fail(__('The :attribute must be unique across different languages.'));
                    }
                }
            };
        }
        $rules['priorityEdit'] = ['required'];
        $rules['statusEdit'] = ['required', 'in:0,1'];
        return $rules;
    }
    
    protected function rulesForSubCategories()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            if (!isset($this->subNames[$locale])) {
                $this->subNames[$locale] = ''; // Initialize if not set
            }
    
            $rules['subNames.' . $locale] = [
                'required',
                'string',
                'min:2',
                Rule::unique('sub_category_translations', 'name')
                    ->where('locale', $locale)
            ];
    
            // Add custom uniqueness check across locales
            $rules['subNames.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && isset($this->subNames[$otherLocale]) && $this->subNames[$locale] === $this->subNames[$otherLocale]) {
                        $fail(__('The :attribute must be unique across different languages.'));
                    }
                }
            };
        }
        return $rules;
    }
    
    protected function rulesForSubCategoriesEdit()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['subNamesEdit.' . $locale] = [
                'required',
                'string',
                'min:2',
                Rule::unique('sub_category_translations', 'name')
                    ->where('locale', $locale)
            ];
    
            // Add custom uniqueness check across locales
            $rules['subNamesEdit.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $this->subNamesEdit[$locale] === $this->subNamesEdit[$otherLocale]) {
                        $fail(__('The :attribute must be unique across different languages.'));
                    }
                }
            };
        }
        $rules['priorityEdit'] = ['required'];
        $rules['statusEdit'] = ['required', 'in:0,1'];
        return $rules;
    }

    // Real-Time Validation
    public function updated($propertyName)
    {
        // $this->validateOnly($propertyName);
        if($this->currentValidation == 'categoryAdd') {
            $this->validateOnly($propertyName, $this->rulesForCategory());
        } else if ($this->currentValidation == 'subCategoryAdd') {
            $this->validateOnly($propertyName, $this->rulesForSubCategories());
        } else if ($this->currentValidation == 'categoryEdit') {
            $this->validateOnly($propertyName, $this->rulesForCategoryEdit());
        } else {
            $this->validateOnly($propertyName, $this->rulesForSubCategoriesEdit());
        }
    }
    

    // CRUD
    public function addCategory()
    {
        try {

            $this->currentValidation = 'categoryAdd';
            $validatedData = $this->validate($this->rulesForCategory());

            try {
                if($this->objectName) {
                    $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->objectData));
                    Storage::disk('s3')->put($this->objectName, $croppedImage , 'public');
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, Please Uplaod The Image')]);
                    return;
                }
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
            }


            $category = Category::create([
                'created_by_id' => auth('admin')->id() ?? 1,
                'updated_by_id' => auth('admin')->id() ?? 1,
                'priority' => Category::max('priority') + 1,
                'image' => $this->objectName,
            ]);

            foreach ($this->filteredLocales as $locale) {
                CategoryTranslation::create([
                    'category_id' => $category->id,
                    'locale' => $locale,
                    'name' => $this->names[$locale],
                    'slug' => Str::slug($this->names[$locale], '-', ''),
                ]);
            }

            $this->emit('categoryAdded');
            $this->resetInput();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Category Added Successfully')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something Went Wrong: ' . $e->getMessage())]);
        }
    }

    
    public function editCategory(int $id) {
        $this->currentValidation = 'categoryEdit';

        $category_edit = Category::find($id);
        $this->category_update = $category_edit;

        $this->de = 0;

        if ($category_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = CategoryTranslation::where('category_id', $category_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->namesEdit[$locale] = $translation->name;
                    $this->slugEdit[$locale] = $translation->slug;
                } else {
                    $this->namesEdit[$locale] = 'Not Found';
                    $this->slugEdit[$locale] = 'Not Found';
                }
            }
            $this->priorityEdit = $category_edit->priority;
            $this->statusEdit = $category_edit->status;
            $this->objectReader = $category_edit->image;

            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Data Successfuly Loaded')]);
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public function updateCategory () {
        $validatedData = $this->validate($this->rulesForCategoryEdit());

        try {
            if($this->objectData) {
                $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->objectData));

                if($this->objectReader){
                        Storage::disk('s3')->delete($this->objectReader);
                        Storage::disk('s3')->put($this->objectName, $croppedImage , 'public');
                    } else {
                        Storage::disk('s3')->put($this->objectName, $croppedImage , 'public');
                    }
                } else {
                $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Image Did Not Update')]);
                // $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, Please Upload New Image')]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
            return;
        }

        Category::where('id', $this->category_update->id)->update([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
            'image' => $this->objectName ?? $this->objectReader,
        ]);
    
        $category = Category::find($this->category_update->id);

        foreach ($this->filteredLocales as $locale) {
            CategoryTranslation::updateOrCreate(
                [
                    'category_id' => $category->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->namesEdit[$locale],
                    'slug' => Str::slug($this->namesEdit[$locale], '-', ''),
                ]
            );
        }

        $this->closeModal();
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Category Updated Successfully')]);
    }

    public function setSubCategory(int $cat_id)
    {
        $this->priorityEdit = SubCategory::count() + 1;
        $this->statusEdit = 1;
        $this->subaddid = $cat_id;

        $this->currentValidation = 'subCategoryAdd';
        $validatedData = $this->validate($this->rulesForSubCategories());
    }

    public function addSubCategory()
    {
        $this->currentValidation = 'subCategoryAdd';
        $validatedData = $this->validate($this->rulesForSubCategories());
        try { 
            $subcategory = SubCategory::create([
                'created_by_id' => auth('admin')->id() ?? 1,
                'updated_by_id' => auth('admin')->id() ?? 1,
                'category_id' => $this->subaddid,
                'priority' => $this->priorityEdit,
                'status' => $this->statusEdit,
            ]);

            foreach ($this->filteredLocales as $locale) {
                SubCategoryTranslation::create([
                    'sub_category_id' => $subcategory->id,
                    'locale' => $locale,
                    'name' => $this->subNames[$locale],
                    'slug' => Str::slug($this->subNames[$locale], '-', ''),
                ]);
            }
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Sub-Category Added Successfully')]);
        } catch (\Exception $e) {
            
        }
    }

    public function editSubCategory(int $id) {
        $this->currentValidation = 'subaCtegoryEdit';

        $sub_category_edit = SubCategory::find($id);
        $this->sub_category_update = $sub_category_edit;

        if ($sub_category_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = SubCategoryTranslation::where('sub_category_id', $sub_category_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->subNamesEdit[$locale] = $translation->name;
                } else {
                    $this->subNamesEdit[$locale] = 'Not Found';
                }
            }
            $this->priorityEdit = $sub_category_edit->priority;
            $this->statusEdit = $sub_category_edit->status;
            // $this->objectReader = $sub_category_edit->image;

            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Data Successfuly Loaded')]);
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public function updateSubCategory () {
        $validatedData = $this->validate($this->rulesForSubCategoriesEdit());

        SubCategory::where('id', $this->sub_category_update->id)->update([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
            'image' => $this->objectName ?? $this->objectReader,
        ]);
    
        $category = SubCategory::find($this->sub_category_update->id);

        foreach ($this->filteredLocales as $locale) {
            SubCategoryTranslation::updateOrCreate(
                [
                    'sub_category_id' => $category->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->subNamesEdit[$locale],
                    'slug' => Str::slug($this->subNamesEdit[$locale], '-', ''),
                ]
            );
        }

        $this->closeModal();
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Sub-Category Updated Successfully')]);
    }

    
    public function updateCategoryOrderA($categories)
    {
        if (!isset($categories)) {
            return; // Handle case where categories key might be missing
        }
    
        // Update categories order
        foreach ($categories as $index => $category) {
            $categoryModel = Category::find($category['id']);
            $categoryModel->update(['priority' => $index]);
        }
    }

    public function deleteCategory (int $id) {
        $this->category_selected_id_delete = Category::find($id);
        $this->category_name_selected_delete = CategoryTranslation::where('category_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->category_name_selected_delete) {
            $this->showTextTemp = $this->category_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $categoryNameToDelete = '';
    public function destroyCategory () {
        if ($this->confirmDelete && $this->categoryNameToDelete === $this->showTextTemp) {
            try {
                if($this->category_selected_id_delete->image) {
                    Storage::disk('s3')->delete($this->category_selected_id_delete->image);
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Something Went Wrong, Image Did Not Removed From Server')]);
                }
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
                return;
            }
            Category::find($this->category_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Category Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->category_selected_id_delete = null;
            $this->category_name_selected_delete = null;
            $this->categoryNameToDelete = '';
            $this->showTextTemp = null;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Operaiton Faild')]);
        }
    }




    public function deleteSubCategory (int $id) {
        $this->subCategory_selected_id_delete = SubCategory::find($id);
        $this->subCategory_name_selected_delete = SubCategoryTranslation::where('sub_category_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->subCategory_name_selected_delete) {
            $this->showTextTemp = $this->subCategory_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $subCategoryNameToDelete = '';
    public function destroySubCategory () {
        if ($this->confirmDelete && $this->subCategoryNameToDelete === $this->showTextTemp) {
            // try {
            //     if($this->subCategory_selected_id_delete->image) {
            //         Storage::disk('s3')->delete($this->subCategory_selected_id_delete->image);
            //     } else {
            //         $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Something Went Wrong, Image Did Not Removed From Server')]);
            //     }
            // } catch (\Exception $e) {
            //     $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
            //     return;
            // }
            SubCategory::find($this->subCategory_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Sub-Category Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->subCategory_selected_id_delete = null;
            $this->subCategory_name_selected_delete = null;
            $this->subCategoryNameToDelete = '';
            $this->showTextTemp = null;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Operaiton Faild')]);
        }
    }
    
    public function sadImage () { 
        $this->de = 1;
    }


    // CRUD HANDLERS
    public function handleCroppedImage($base64data)
    {
        // dd($base64data);
        if ($base64data){
            $microtime = str_replace('.', '', microtime(true));
            $this->objectData = $base64data;
            $this->objectName = 'categories/' . ($this->names[app()->getLocale()] ?? $this->namesEdit[app()->getLocale()] ?? 'categories') . '_category_'.date('Ydm') . $microtime . '.png';
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return;
            // return 'failed to crop image code...405';
        }
    }

    public function updateSubCategoryOrderA($subCategories, $newParentId)
    {
        if (!isset($subCategories)) {
            return; // Handle case where subCategories key might be missing
        }
    
        // Update sub-categories order based on their parent category
        foreach ($subCategories as $subCategory) {
            $subCategoryModel = SubCategory::find($subCategory['id']);
            $subCategoryModel->update([
                'priority' => $subCategory['index'],
                'category_id' => $subCategory['parentId'], // Update the parent category if it changed
            ]);
        }
    }
    
    public function updateCatStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $catStatus = Category::find($id);
    
        if ($catStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $catStatus->status = !$catStatus->status;
            $catStatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function updateSubCatStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $subCatStatus = SubCategory::find($id);
    
        if ($subCatStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $subCatStatus->status = !$subCatStatus->status;
            $subCatStatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }


    // RESET BUTTON
    public function reSyncA(){
        $this->render();
    }
    
    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
        $this->de = 1;
    }

    public function resetInput()
    {
        foreach ($this->filteredLocales as $locale) {
            $this->names[$locale] = "";
            $this->namesEdit[$locale] = "";
        }
        $this->status = 1;
        // $this->priority = Brand::max('priority') + 1;
        $this->statusEdit = 1;
        $this->priorityEdit = '';
        $this->objectReader = null;
        $this->objectName = null;
        $this->objectData = null;

        $this->emit('resetData');
        $this->emit('resetEditData');
    }


     // Render
     public function render(){ 

        $categoriesData = Category::with([
            'subCategory' => function ($query) {
                $query->with(['subCategoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                }])->orderBy('priority');
            },
            'categoryTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }
        ])->orderBy('priority')->get();

        return view('super-admins.pages.categories.category-structure', [
            'categoriesData' => $categoriesData,
            'objectReader' => $this->objectReader
        ]);
    }

}