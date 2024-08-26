<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\VariationSize;
use App\Models\VariationSizeTranslation;

class SizeLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $sizes = [];
    public $slugs = [];
    public $code;
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $sizesEdit = [];
    public $slugsEdit = [];
    public $codeEdit;
    public $priorityEdit;
    public $statusEdit;
    public $size_update;
    // DELETE
    public $size_selected_id_delete;
    public $size_name_selected_delete;
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
        'filtersizes' => 'filtersizes',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = VariationSize::max('priority') + 1;
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
            $rules['sizes.' . $locale] = 'required|string|min:1';
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
            $rules['sizesEdit.' . $locale] = 'required|string|min:1';
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
    public function saveSize () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());

        $size = VariationSize::create([
            'created_by_id' => 1,
            'updated_by_id' => 1,
            'code' => $validatedData['code'],
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            VariationSizeTranslation::create([
                'variation_size_id' => $size->id,
                'locale' => $locale,
                'name' => $this->sizes[$locale],
            ]);
        }

        $this->resetInput();
        $this->filterSizes($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Size Added Successfully')]);
    }

    public function editSize(int $id) {
        $this->currentValidation = 'edit';

        $size_edit = VariationSize::find($id);
        $this->size_update = $size_edit;

        if ($size_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = VariationSizeTranslation::where('variation_size_id', $size_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->sizesEdit[$locale] = $translation->name;
                } else {
                    $this->sizesEdit[$locale] = 'Not Found';
                }
            }
            $this->codeEdit = $size_edit->code;
            $this->priorityEdit = $size_edit->priority;
            $this->statusEdit = $size_edit->status;


        } else {
        // error message
        }

    }
    public function updateSize () {
        $validatedData = $this->validate($this->rulesForUpdate());

        VariationSize::where('id', $this->size_update->id)->update([
            'created_by_id' => 1,
            'updated_by_id' => 1,
            'code' => $validatedData['codeEdit'],
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
        ]);
    
        $size = VariationSize::find($this->size_update->id);

        foreach ($this->filteredLocales as $locale) {
            VariationSizeTranslation::updateOrCreate(
                [
                    'variation_size_id' => $size->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->sizesEdit[$locale],
                ]
            );
        }

        $this->closeModal();
        $this->filterSizes($this->statusFilter);
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Size Updated Successfully')]);
    }

    public function removeSize(int $id) {
        $this->size_selected_id_delete = VariationSize::find($id);
        $this->size_name_selected_delete = VariationSizeTranslation::where('variation_size_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->size_name_selected_delete) {
            $this->showTextTemp = $this->size_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $sizeNameToDelete = '';
    public function destroySize () {
        if ($this->confirmDelete && $this->sizeNameToDelete === $this->showTextTemp) {

            VariationSize::find($this->size_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Size Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->size_selected_id_delete = null;
            $this->size_name_selected_delete = null;
            $this->sizeNameToDelete = '';
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
    
        // Find the Size by ID
        $size = VariationSize::find($p_id);
        
        if ($size) {
            $size->priority = $updatedPriority;
            $size->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Size not found')
            ]);
        }
    }

    public function updateStatus(int $id)
    {
        // Find the Size by ID, if not found return an error
        $sizestatus = VariationSize::find($id);
    
        if ($sizestatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $sizestatus->status = !$sizestatus->status;
            $sizestatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Size Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Size not found')
            ]);
        }
    }

    // CRUD Handler
    public function filterSizes($status)
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
            $this->sizes[$locale] = "";
            $this->sizesEdit[$locale] = "";
        }
        $this->code = '';
        $this->codeEdit = '';
        $this->status = 1;
        $this->priority = VariationSize::max('priority') + 1;
        $this->statusEdit = 1;
        $this->priorityEdit = '';
    }

    // Render
    public function render(){
        $this->activeCount = VariationSize::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = VariationSize::where('status', 0)->count() ?? 0;

        $query = VariationSize::with(['variationSizeTranslation' => function ($query) {
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
            $query->whereHas('variationSizeTranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10); 

        return view('super-admins.pages.sizes.size-table', [
            'tableData' => $tableData,
        ]);
    }
}