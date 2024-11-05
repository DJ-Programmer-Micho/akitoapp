<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\VariationMaterial;
use App\Models\VariationMaterialTranslation;

class MaterialLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $materials = [];
    public $slugs = [];
    public $code;
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $materialsEdit = [];
    public $slugsEdit = [];
    public $codeEdit;
    public $priorityEdit;
    public $statusEdit;
    public $material_update;
    // DELETE
    public $material_selected_id_delete;
    public $material_name_selected_delete;
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
    // VALIDATION
    public $currentValidation = 'add';
    //LISTENERS
    protected $listeners = [
        'filtermaterials' => 'filtermaterials',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = VariationMaterial::max('priority') + 1;
        $this->status = 1;
        $this->statusFilter = request()->query('statusFilter', 'all');
        $this->page = request()->query('page', 1);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Page Loaded Added Successfully')]);
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
            $rules['materials.' . $locale] = [
                'required',
                'string',
                'min:2',
                Rule::unique('variation_material_translations', 'name')
                    ->where('locale', $locale)
            ];
    
            // Add a closure to ensure materials are compared only if they exist
            $rules['materials.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                // Check that materials exist for the locale before accessing
                if (isset($this->materials[$locale])) {
                    foreach ($this->filteredLocales as $otherLocale) {
                        if ($locale !== $otherLocale && isset($this->materials[$otherLocale]) && $this->materials[$locale] === $this->materials[$otherLocale]) {
                            $fail(__('The :attribute must be unique across different languages.'));
                        }
                    }
                }
            };
        }
        $rules['code'] = ['required'];
        $rules['priority'] = ['required'];
        $rules['status'] = ['required'];
        return $rules;
    }
    
    protected function rulesForUpdate()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['materialsEdit.' . $locale] = [
                'required',
                'string',
                'min:2',
                Rule::unique('variation_material_translations', 'name')
                    ->where('locale', $locale)
                    ->ignore($this->material_update->id, 'variation_material_id')
            ];
    
            // Add custom uniqueness check across locales
            $rules['materialsEdit.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $this->materialsEdit[$locale] === $this->materialsEdit[$otherLocale]) {
                        $fail(__('The :attribute must be unique across different languages.'));
                    }
                }
            };
        }
        $rules['codeEdit'] = ['required'];
        $rules['priorityEdit'] = ['required'];
        $rules['statusEdit'] = ['required'];
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
    public function saveMaterial () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());

        $material = VariationMaterial::create([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'code' => $validatedData['code'],
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            VariationMaterialTranslation::create([
                'variation_material_id' => $material->id,
                'locale' => $locale,
                'name' => $this->materials[$locale],
            ]);
        }

        $this->closeModal();
        $this->filterMaterials($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Material Added Successfully')]);
    }

    public function editMaterial(int $id) {
        $this->currentValidation = 'edit';

        $material_edit = VariationMaterial::find($id);
        $this->material_update = $material_edit;

        if ($material_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = VariationMaterialTranslation::where('variation_material_id', $material_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->materialsEdit[$locale] = $translation->name;
                } else {
                    $this->materialsEdit[$locale] = 'Not Found';
                }
            }
            $this->codeEdit = $material_edit->code;
            $this->priorityEdit = $material_edit->priority;
            $this->statusEdit = $material_edit->status;


        } else {
        // error message
        }

    }
    public function updateMaterial () {
        $validatedData = $this->validate($this->rulesForUpdate());

        VariationMaterial::where('id', $this->material_update->id)->update([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'code' => $validatedData['codeEdit'],
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
        ]);
    
        $material = VariationMaterial::find($this->material_update->id);

        foreach ($this->filteredLocales as $locale) {
            VariationMaterialTranslation::updateOrCreate(
                [
                    'variation_material_id' => $material->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->materialsEdit[$locale],
                ]
            );
        }

        $this->closeModal();
        $this->filterMaterials($this->statusFilter);
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Material Updated Successfully')]);
    }

    public function removeMaterial(int $id) {
        $this->material_selected_id_delete = VariationMaterial::find($id);
        $this->material_name_selected_delete = VariationMaterialTranslation::where('variation_material_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->material_name_selected_delete) {
            $this->showTextTemp = $this->material_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $materialNameToDelete = '';
    public function destroyMaterial () {
        if ($this->confirmDelete && $this->materialNameToDelete === $this->showTextTemp) {

            VariationMaterial::find($this->material_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Material Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->material_selected_id_delete = null;
            $this->material_name_selected_delete = null;
            $this->materialNameToDelete = '';
            $this->showTextTemp = null;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Operaiton Faild')]);
        }
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
    
        // Find the Material by ID
        $material = VariationMaterial::find($p_id);
        
        if ($material) {
            $material->priority = $updatedPriority;
            $material->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Material not found')
            ]);
        }
    }

    public function updateStatus(int $id)
    {
        // Find the Material by ID, if not found return an error
        $materialstatus = VariationMaterial::find($id);
    
        if ($materialstatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $materialstatus->status = !$materialstatus->status;
            $materialstatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Material Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Material not found')
            ]);
        }
    }

    // CRUD Handler
    public function filterMaterials($status)
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
            $this->materials[$locale] = "";
            $this->materialsEdit[$locale] = "";
        }
        $this->code = '';
        $this->codeEdit = '';
        $this->status = 1;
        $this->priority = VariationMaterial::max('priority') + 1;
        $this->statusEdit = 1;
        $this->priorityEdit = '';
    }

    // Render
    public function render(){
        $this->activeCount = VariationMaterial::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = VariationMaterial::where('status', 0)->count() ?? 0;

        $query = VariationMaterial::with(['variationMaterialTranslation' => function ($query) {
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
            $query->whereHas('variationMaterialTranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10); 

        return view('super-admins.pages.materials.material-table', [
            'tableData' => $tableData,
        ]);
    }
}