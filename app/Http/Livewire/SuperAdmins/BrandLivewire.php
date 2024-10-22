<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Brand;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Models\BrandTranslation;
use Illuminate\Support\Facades\Storage;

class BrandLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $brands = [];
    public $slugs = [];
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $brandsEdit = [];
    public $priorityEdit;
    public $statusEdit;
    public $brand_update;
    // DELETE
    public $brand_selected_id_delete;
    public $brand_name_selected_delete;
    public $showTextTemp;
    public $confirmDelete;
    // Render
    public $search = '';
    public $statusFilter = 'all';
    public $page = 1;
    public $activeCount = 0;
    public $nonActiveCount = 0;
    // Temp
    public $de = 1;
    public $objectReader;
    public $objectName;
    public $objectData;
    // VALIDATION
    public $currentValidation = 'add';
    //LISTENERS
    protected $listeners = [
        'imgCrop' => 'handleCroppedImage',
        'filterBrands' => 'filterBrands',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = Brand::max('priority') + 1;
        $this->status = 1;
        $this->statusFilter = request()->query('statusFilter', 'all');
        $this->page = request()->query('page', 1);
    }

    // VALIDATION
    protected function rules()
    {
        //Keep It Empty
    }

    protected function rulesForSave()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['brands.' . $locale] =  [
                'required',
                'string',
                'min:1',
                Rule::unique('brand_translations', 'name')
                    ->where('locale', $locale)
            ];
        }
        $rules['priority'] = ['required'];
        $rules['status'] = ['required','in:0,1'];
        return $rules;
    }

    protected function rulesForUpdate()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['brandsEdit.' . $locale] = [
                'required',
                'string',
                'min:1',
                Rule::unique('brand_translations', 'name')
                    ->where('locale', $locale)
            ];
        }
        $rules['priorityEdit'] = ['required'];
        $rules['statusEdit'] = ['required','in:0,1'];
        return $rules;
    }

    // protected $messages = [
    //     'email.required' => 'The Email Address cannot be empty.',
    //     'email.email' => 'The Email Address format is not valid.',
    // ];

    // Real-Time Validation
    public function updated($propertyName)
    {
        // $this->validateOnly($propertyName);
        if($this->currentValidation == 'add') {
            $this->validateOnly($propertyName, $this->rulesForSave());
        } else {
            $this->validateOnly($propertyName, $this->rulesForUpdate());
        }
    }

        
    //CRUD
    public function saveBrand () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());

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
        
        $brand = Brand::create([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
            'image' => $this->objectName,
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            BrandTranslation::create([
                'brand_id' => $brand->id,
                'locale' => $locale,
                'name' => $this->brands[$locale],
                'slug' => Str::slug($this->brands[$locale], '-', ''),
            ]);
        }

        $this->resetInput();
        $this->filterBrands($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Brand Added Successfully')]);
    }
    public function editBrand(int $id) {
        $this->currentValidation = 'edit';
        // dd('you clicked me');
        $this->de = 0;
        // $a = Brand::where('id',$id)->first();
        $brand_edit = Brand::find($id);
        $this->brand_update = $brand_edit;

        if ($brand_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = BrandTranslation::where('brand_id', $brand_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->brandsEdit[$locale] = $translation->name;
                } else {
                    $this->brandsEdit[$locale] = 'Not Found';
                }
            }
            $this->priorityEdit = $brand_edit->priority;
            $this->statusEdit = $brand_edit->status;
            $this->objectReader = $brand_edit->image;
          

        } else {
        // error message
        }

    }
    public function updateBrand () {
        $validatedData = $this->validate($this->rulesForUpdate());

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

        Brand::where('id', $this->brand_update->id)->update([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
            'image' => $this->objectName ?? $this->objectReader,
        ]);
    
        $brand = Brand::find($this->brand_update->id);

        foreach ($this->filteredLocales as $locale) {
            BrandTranslation::updateOrCreate(
                [
                    'brand_id' => $brand->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->brandsEdit[$locale],
                    'slug' => Str::slug($this->brandsEdit[$locale], '-', ''),
                ]
            );
        }

        $this->closeModal();
        $this->filterBrands($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Brand Updated Successfully')]);
    }
    public function removeBrand (int $id) {
        $this->brand_selected_id_delete = Brand::find($id);
        $this->brand_name_selected_delete = BrandTranslation::where('brand_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->brand_name_selected_delete) {
            $this->showTextTemp = $this->brand_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $brandNameToDelete = '';
    public function destroyBrand () {
        if ($this->confirmDelete && $this->brandNameToDelete === $this->showTextTemp) {
            try {
                if($this->brand_selected_id_delete->image) {
                    Storage::disk('s3')->delete($this->brand_selected_id_delete->image);
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Something Went Wrong, Image Did Not Removed From Server')]);
                }
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
                return;
            }
            Brand::find($this->brand_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Brand Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->brand_selected_id_delete = null;
            $this->brand_name_selected_delete = null;
            $this->brandNameToDelete = '';
            $this->showTextTemp = null;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Operaiton Faild')]);
        }
    }

    public function sadImage () { 
        $this->de = 1;
    }

    public function updatePriority(int $p_id, $updatedPriority)
    {
        // Validate if updatedPriority is a number
        if (!is_numeric($updatedPriority)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Invalid priority value')
            ]);
            return;
        }
    
        // Find the brand by ID
        $brand = Brand::find($p_id);
        
        if ($brand) {
            $brand->priority = $updatedPriority;
            $brand->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function updateStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $brandStatus = Brand::find($id);
    
        if ($brandStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $brandStatus->status = !$brandStatus->status;
            $brandStatus->save();
    
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

    // CRUD Handler
    public function handleCroppedImage($base64data)
    {
        // dd($base64data);
        if ($base64data){
            $microtime = str_replace('.', '', microtime(true));
            $this->objectData = $base64data;
            $this->objectName = 'brands/' . ($this->brands[app()->getLocale()] ?? $this->brandsEdit[app()->getLocale()] ?? 'brandname') . '_brand_'.date('Ydm') . $microtime . '.png';
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return;
            // return 'failed to crop image code...405';
        }
    }

    public function filterBrands($status)
    {
        $this->statusFilter = $status;
    }

    // QUERY FUNCTION
    public function changeTab($status)
    {
        $this->statusFilter = $status;
        $this->page = 1;
        $this->emitSelf('refresh');
    }

    // RESET BUTTON
    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
        $this->de = 1;
    }
 
    public function resetInput()
    {
        foreach ($this->filteredLocales as $locale) {
            $this->brands[$locale] = "";
            $this->brandsEdit[$locale] = "";
        }
        $this->status = 1;
        $this->priority = Brand::max('priority') + 1;
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
        $this->activeCount = Brand::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = Brand::where('status', 0)->count() ?? 0;

        $query = Brand::with(['brandtranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }]);
        
        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('status', 1);
        } elseif ($this->statusFilter === 'non-active') {
            $query->where('status', 0);
        }
    
        // Apply search filter based on translations
        if (!empty($this->search)) {
            $query->whereHas('brandtranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10)->withQueryString(); 

        return view('super-admins.pages.brands.brand-table', [
            'tableData' => $tableData,
            'objectReader' => $this->objectReader
        ]);
    }
}