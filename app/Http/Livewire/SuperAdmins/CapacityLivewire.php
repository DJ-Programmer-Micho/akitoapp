<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\VariationCapacity;
use App\Models\VariationCapacityTranslation;

class CapacityLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $capacities = [];
    public $slugs = [];
    public $code;
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $capacitiesEdit = [];
    public $slugsEdit = [];
    public $codeEdit;
    public $priorityEdit;
    public $statusEdit;
    public $capacity_update;
    // DELETE
    public $capacity_selected_id_delete;
    public $capacity_name_selected_delete;
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
        'filtercapacities' => 'filtercapacities',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = VariationCapacity::max('priority') + 1;
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
            $rules['capacities.' . $locale] = [
                'required',
                'string',
                'min:1',
                Rule::unique('capacity_translations', 'name')
                    ->where('locale', $locale)
            ];

            $rules['capacities.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $this->capacities[$locale] === $this->capacities[$otherLocale]) {
                        $fail(__('The :attribute must be unique across different languages.'));
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
            $rules['capacitiesEdit.' . $locale] = [
                'required',
                'string',
                'min:1',
                Rule::unique('variation_capacity_translations', 'name')
                    ->where('locale', $locale)
                    ->ignore($this->capacity_update->id, 'variation_capacity_id')
            ];
    
            // Add custom uniqueness check across locales
            $rules['capacitiesEdit.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $this->capacitiesEdit[$locale] === $this->capacitiesEdit[$otherLocale]) {
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
    public function saveCapacity () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());

        $capacity = VariationCapacity::create([
            'created_by_id' => 1,
            'updated_by_id' => 1,
            'code' => $validatedData['code'],
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            VariationCapacityTranslation::create([
                'variation_capacity_id' => $capacity->id,
                'locale' => $locale,
                'name' => $this->capacities[$locale],
            ]);
        }

        $this->closeModal();
        $this->filterCapacities($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Capacity Added Successfully')]);
    }

    public function editCapacity(int $id) {
        $this->currentValidation = 'edit';

        $capacity_edit = VariationCapacity::find($id);
        $this->capacity_update = $capacity_edit;

        if ($capacity_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = VariationCapacityTranslation::where('variation_capacity_id', $capacity_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->capacitiesEdit[$locale] = $translation->name;
                } else {
                    $this->capacitiesEdit[$locale] = 'Not Found';
                }
            }
            $this->codeEdit = $capacity_edit->code;
            $this->priorityEdit = $capacity_edit->priority;
            $this->statusEdit = $capacity_edit->status;


        } else {
        // error message
        }

    }
    public function updateCapacity () {
        $validatedData = $this->validate($this->rulesForUpdate());

        VariationCapacity::where('id', $this->capacity_update->id)->update([
            'created_by_id' => 1,
            'updated_by_id' => 1,
            'code' => $validatedData['codeEdit'],
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
        ]);
    
        $capacity = VariationCapacity::find($this->capacity_update->id);

        foreach ($this->filteredLocales as $locale) {
            VariationCapacityTranslation::updateOrCreate(
                [
                    'variation_capacity_id' => $capacity->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->capacitiesEdit[$locale],
                ]
            );
        }

        $this->closeModal();
        $this->filterCapacities($this->statusFilter);
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Capacity Updated Successfully')]);
    }

    public function removeCapacity(int $id) {
        $this->capacity_selected_id_delete = VariationCapacity::find($id);
        $this->capacity_name_selected_delete = VariationCapacityTranslation::where('variation_capacity_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->capacity_name_selected_delete) {
            $this->showTextTemp = $this->capacity_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $capacityNameToDelete = '';
    public function destroyCapacity () {
        if ($this->confirmDelete && $this->capacityNameToDelete === $this->showTextTemp) {

            VariationCapacity::find($this->capacity_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Capacity Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->capacity_selected_id_delete = null;
            $this->capacity_name_selected_delete = null;
            $this->capacityNameToDelete = '';
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
    
        // Find the Capacity by ID
        $capacity = VariationCapacity::find($p_id);
        
        if ($capacity) {
            $capacity->priority = $updatedPriority;
            $capacity->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Capacity not found')
            ]);
        }
    }

    public function updateStatus(int $id)
    {
        // Find the Capacity by ID, if not found return an error
        $capacitiestatus = VariationCapacity::find($id);
    
        if ($capacitiestatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $capacitiestatus->status = !$capacitiestatus->status;
            $capacitiestatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Capacity Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Capacity not found')
            ]);
        }
    }

    // CRUD Handler
    public function filterCapacities($status)
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
            $this->capacities[$locale] = "";
            $this->capacitiesEdit[$locale] = "";
        }
        $this->code = '';
        $this->codeEdit = '';
        $this->status = 1;
        $this->priority = VariationCapacity::max('priority') + 1;
        $this->statusEdit = 1;
        $this->priorityEdit = '';
    }

    // Render
    public function render(){
        $this->activeCount = VariationCapacity::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = VariationCapacity::where('status', 0)->count() ?? 0;

        $query = VariationCapacity::with(['variationCapacityTranslation' => function ($query) {
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
            $query->whereHas('variationCapacityTranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10); 

        return view('super-admins.pages.capacities.capacity-table', [
            'tableData' => $tableData,
        ]);
    }
}