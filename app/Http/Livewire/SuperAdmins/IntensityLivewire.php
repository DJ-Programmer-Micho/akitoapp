<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\VariationIntensity;
use Illuminate\Validation\Rule;

class IntensityLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $minVal;
    public $maxVal;
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $minValEdit;
    public $maxValEdit;
    public $slugsEdit = [];
    public $codeEdit;
    public $priorityEdit;
    public $statusEdit;
    public $size_update;
    // DELETE
    public $intesity_selected_id_delete;
    public $intesity_name_selected_delete;
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
        'filterIntensity' => 'filterIntensity',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = VariationIntensity::max('priority') + 1;
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
        $rules['minVal'] = ['required'];
        $rules['maxVal'] = ['required'];
        $rules['priority'] = ['required'];
        $rules['status'] = ['required'];
        return $rules;
    }
    protected function rulesForUpdate()
    {
        $rules = [];
        $rules['minValEdit'] = ['required'];
        $rules['maxValEdit'] = ['required'];
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
    public function saveIntensity () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());

        VariationIntensity::create([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'min' => $validatedData['minVal'],
            'max' => $validatedData['maxVal'],
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
        ]);

        $this->closeModal();
        $this->filterIntensity($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Intensity Added Successfully')]);
    }

    // TO DO
    public function editIntensity(int $id) {
        $this->currentValidation = 'edit';

        $intisity_edit = VariationIntensity::find($id);
        $this->size_update = $intisity_edit;

        if ($intisity_edit) {
            $this->minValEdit = $intisity_edit->min;
            $this->maxValEdit = $intisity_edit->max;
            $this->priorityEdit = $intisity_edit->priority;
            $this->statusEdit = $intisity_edit->status;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Intensity Not Found')]);
            $this->closeModal();
        }

    }
    public function updateIntensity() {
        $validatedData = $this->validate($this->rulesForUpdate());

        VariationIntensity::where('id', $this->size_update->id)->update([
            'updated_by_id' => auth('admin')->id() ?? 1,
            'min' => $validatedData['minValEdit'],
            'max' => $validatedData['maxValEdit'],
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
        ]);

        $this->closeModal();
        $this->filterIntensity($this->statusFilter);
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Intensity Updated Successfully')]);
    }

    public function removeIntensity(int $id) {
        $this->intesity_selected_id_delete = VariationIntensity::find($id);
        $this->intesity_name_selected_delete = "Delete";
        if ($this->intesity_name_selected_delete) {
            $this->showTextTemp = $this->intesity_name_selected_delete;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $intensityNameToDelete = '';
    public function destroyIntensity () {
        if ($this->confirmDelete && $this->intensityNameToDelete === $this->showTextTemp) {

            VariationIntensity::find($this->intesity_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Size Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->intesity_selected_id_delete = null;
            $this->intesity_name_selected_delete = null;
            $this->intensityNameToDelete = '';
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
    
        // Find the intensity by ID
        $intensity = VariationIntensity::find($p_id);
        
        if ($intensity) {
            $intensity->priority = $updatedPriority;
            $intensity->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Intensity not found')
            ]);
        }
    }

    public function updateStatus(int $id)
    {
        // Find the intensity by ID, if not found return an error
        $intensitystatus = VariationIntensity::find($id);
    
        if ($intensitystatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $intensitystatus->status = !$intensitystatus->status;
            $intensitystatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Intensity Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Intensity not found')
            ]);
        }
    }

    // CRUD Handler
    public function filterIntensity($status)
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
        $this->minVal = null;
        $this->maxVal = null;
        $this->maxValEdit = null;
        $this->maxValEdit = null;
        $this->status = 1;
        $this->priority = VariationIntensity::max('priority') + 1;
        $this->statusEdit = 1;
        $this->priorityEdit = null;
    }

    // Render
    public function render(){
        $this->activeCount = VariationIntensity::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = VariationIntensity::where('status', 0)->count() ?? 0;

        $query = VariationIntensity::query();
        
        
        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('status', 1);
        } elseif ($this->statusFilter === 'non-active') {
            $query->where('status', 0);
        }
    
        // Apply search filter based on translations
        if (!empty($this->search)) {
            $query->whereHas('variationIntensity', function ($query) {
                $query->where('min', 'like', '%' . $this->search . '%')
                    ->orWhere('max', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10); 

        return view('super-admins.pages.intensities.intensity-table', [
            'tableData' => $tableData,
        ]);
    }
}