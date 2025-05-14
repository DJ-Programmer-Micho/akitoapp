<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Models\ComingSoon;
use App\Models\ComingSoonTranslation;
use Illuminate\Support\Facades\Storage;

class ComingSoonLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $soons = [];
    public $slugs = [];
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $soonsEdit = [];
    public $priorityEdit;
    public $statusEdit;
    public $soon_update;
    // DELETE
    public $soon_selected_id_delete;
    public $soon_name_selected_delete;
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
        'filtersoons' => 'filterSoons',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = ComingSoon::max('priority') + 1;
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
            $rules['soons.' . $locale] =  [
                'required',
                'string',
                'min:1',
                Rule::unique('coming_soon_translations', 'name')
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
            $rules['soonsEdit.' . $locale] = [
                'required',
                'string',
                'min:1',
                Rule::unique('coming_soon_translations', 'name')
                    ->where('locale', $locale)
                    ->ignore($this->soon_update->id, 'coming_soon_id')
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
    public function saveSoon () {
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
        
        $soon = ComingSoon::create([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
            'image' => $this->objectName,
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            ComingSoonTranslation::create([
                'coming_soon_id' => $soon->id,
                'locale' => $locale,
                'name' => $this->soons[$locale],
                'slug' => Str::slug($this->soons[$locale], '-', ''),
            ]);
        }

        $this->resetInput();
        $this->filterSoons($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Coming Soon Added Successfully')]);
    }
    public function editSoon(int $id) {
        $this->currentValidation = 'edit';
        $this->de = 0;
        $soon_edit = ComingSoon::find($id);
        $this->soon_update = $soon_edit;

        if ($soon_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = ComingSoonTranslation::where('coming_soon_id', $soon_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->soonsEdit[$locale] = $translation->name;
                } else {
                    $this->soonsEdit[$locale] = 'Not Found';
                }
            }
            $this->priorityEdit = $soon_edit->priority;
            $this->statusEdit = $soon_edit->status;
            $this->objectReader = $soon_edit->image;
          

        } else {
        // error message
        }

    }
    public function updateSoon () {
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

        ComingSoon::where('id', $this->soon_update->id)->update([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
            'image' => $this->objectName ?? $this->objectReader,
        ]);
    
        $soon = ComingSoon::find($this->soon_update->id);

        foreach ($this->filteredLocales as $locale) {
            ComingSoonTranslation::updateOrCreate(
                [
                    'coming_soon_id' => $soon->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->soonsEdit[$locale],
                    'slug' => Str::slug($this->soonsEdit[$locale], '-', ''),
                ]
            );
        }

        $this->closeModal();
        $this->filterSoons($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Coming Soon Updated Successfully')]);
    }
    public function removeSoon (int $id) {
        $this->soon_selected_id_delete = ComingSoon::find($id);
        $this->soon_name_selected_delete = ComingSoonTranslation::where('coming_soon_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->soon_name_selected_delete) {
            $this->showTextTemp = $this->soon_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $soonNameToDelete = '';
    public function destroySoon () {
        if ($this->confirmDelete && $this->soonNameToDelete === $this->showTextTemp) {
            try {
                if($this->soon_selected_id_delete->image) {
                    Storage::disk('s3')->delete($this->soon_selected_id_delete->image);
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Something Went Wrong, Image Did Not Removed From Server')]);
                }
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
                return;
            }
            ComingSoon::find($this->soon_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Coming Soon Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->soon_selected_id_delete = null;
            $this->soon_name_selected_delete = null;
            $this->soonNameToDelete = '';
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
    
        // Find the soon by ID
        $soon = ComingSoon::find($p_id);
        
        if ($soon) {
            $soon->priority = $updatedPriority;
            $soon->save();
            
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
        // Find the soon by ID, if not found return an error
        $soonStatus = ComingSoon::find($id);
    
        if ($soonStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $soonStatus->status = !$soonStatus->status;
            $soonStatus->save();
    
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
            $this->objectName = 'coming_soons/' . ($this->soons[app()->getLocale()] ?? $this->soonsEdit[app()->getLocale()] ?? 'soonname') . '_soon_'.date('Ydm') . $microtime . '.png';
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return;
            // return 'failed to crop image code...405';
        }
    }

    public function filterSoons($status)
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
            $this->soons[$locale] = "";
            $this->soonsEdit[$locale] = "";
        }
        $this->status = 1;
        $this->priority = ComingSoon::max('priority') + 1;
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
        $this->activeCount = ComingSoon::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = ComingSoon::where('status', 0)->count() ?? 0;

        $query = ComingSoon::with(['coming_soon_translation' => function ($query) {
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
            $query->whereHas('coming_soon_translation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10)->withQueryString(); 

        return view('super-admins.pages.comingsoon.soon-table', [
            'tableData' => $tableData,
            'objectReader' => $this->objectReader
        ]);
    }
}