<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\VariationColor;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;
use App\Models\VariationColorTranslations;

class ColorLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $colors = [];
    public $slugs = [];
    public $code;
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $colorsEdit = [];
    public $slugsEdit = [];
    public $codeEdit;
    public $priorityEdit;
    public $statusEdit;
    public $color_update;
    // DELETE
    public $color_selected_id_delete;
    public $color_name_selected_delete;
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
        'filterColors' => 'filterColors',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = VariationColor::max('priority') + 1;
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
            $rules['colors.' . $locale] = [
                'required',
                'string',
                'min:1',
                Rule::unique('variation_color_translations', 'name')
                ->where('locale', $locale)
            ];

            $rules['colors.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $this->colors[$locale] === $this->colors[$otherLocale]) {
                        $fail(__('The :attribute must be unique across different languages.'));
                    }
                }
            };
        }
        $rules['code'] = ['required','regex:/^#?[0-9A-Fa-f]+$/'];
        $rules['priority'] = ['required'];
        $rules['status'] = ['required'];
        return $rules;
    }
    
    protected function rulesForUpdate()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['colorsEdit.' . $locale] = [
                'required',
                'string',
                'min:2',
                Rule::unique('variation_color_translations', 'name')
                    ->where('locale', $locale)
                    ->ignore($this->color_update->id, 'variation_color_id')
            ];
    
            // Add custom uniqueness check across locales
            $rules['colorsEdit.' . $locale][] = function ($attribute, $value, $fail) use ($locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $this->colorsEdit[$locale] === $this->colorsEdit[$otherLocale]) {
                        $fail(__('The :attribute must be unique across different languages.'));
                    }
                }
            };
        }
        $rules['codeEdit'] = ['required','regex:/^#?[0-9A-Fa-f]+$/'];
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
    public function saveColor () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());

        $color = VariationColor::create([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'code' => $validatedData['code'],
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            VariationColorTranslations::create([
                'variation_color_id' => $color->id,
                'locale' => $locale,
                'name' => $this->colors[$locale],
            ]);
        }

        $this->closeModal();
        $this->filterColors($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Color Added Successfully')]);
    }

    public function editColor(int $id) {
        $this->currentValidation = 'edit';

        $color_edit = VariationColor::find($id);
        $this->color_update = $color_edit;

        if ($color_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = VariationColorTranslations::where('variation_color_id', $color_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->colorsEdit[$locale] = $translation->name;
                } else {
                    $this->colorsEdit[$locale] = 'Not Found';
                }
            }
            $this->codeEdit = $color_edit->code;
            $this->priorityEdit = $color_edit->priority;
            $this->statusEdit = $color_edit->status;


        } else {
        // error message
        }

    }
    public function updateColor () {
        $validatedData = $this->validate($this->rulesForUpdate());

        VariationColor::where('id', $this->color_update->id)->update([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'code' => $validatedData['codeEdit'],
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
        ]);
    
        $color = VariationColor::find($this->color_update->id);

        foreach ($this->filteredLocales as $locale) {
            VariationColorTranslations::updateOrCreate(
                [
                    'variation_color_id' => $color->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->colorsEdit[$locale],
                ]
            );
        }

        $this->closeModal();
        $this->filterColors($this->statusFilter);
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Color Updated Successfully')]);
    }

    public function removeColor(int $id) {
        $this->color_selected_id_delete = VariationColor::find($id);
        $this->color_name_selected_delete = VariationColorTranslations::where('variation_color_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->color_name_selected_delete) {
            $this->showTextTemp = $this->color_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $colorNameToDelete = '';
    public function destroyColor () {
        if ($this->confirmDelete && $this->colorNameToDelete === $this->showTextTemp) {

            VariationColor::find($this->color_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Color Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->color_selected_id_delete = null;
            $this->color_name_selected_delete = null;
            $this->colorNameToDelete = '';
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
    
        // Find the color by ID
        $color = VariationColor::find($p_id);
        
        if ($color) {
            $color->priority = $updatedPriority;
            $color->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Color not found')
            ]);
        }
    }

    public function updateStatus(int $id)
    {
        // Find the color by ID, if not found return an error
        $colorStatus = VariationColor::find($id);
    
        if ($colorStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $colorStatus->status = !$colorStatus->status;
            $colorStatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Color Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Color not found')
            ]);
        }
    }

    // CRUD Handler
    public function filterColors($status)
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
            $this->colors[$locale] = "";
            $this->colorsEdit[$locale] = "";
        }
        $this->code = '';
        $this->codeEdit = '';
        $this->status = 1;
        $this->priority = VariationColor::max('priority') + 1;
        $this->statusEdit = 1;
        $this->priorityEdit = '';
    }

    // Render
    public function render(){
        $this->activeCount = VariationColor::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = VariationColor::where('status', 0)->count() ?? 0;

        $query = VariationColor::with(['variationColorTranslation' => function ($query) {
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
            $query->whereHas('variationColorTranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10); 

        return view('super-admins.pages.colors.color-table', [
            'tableData' => $tableData,
        ]);
    }
}