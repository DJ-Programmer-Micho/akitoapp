<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Ticker;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use App\Models\TickerTranslation;

class TickerLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $tickers = [];
    public $slugs = [];
    public $url;
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $tickersEdit = [];
    public $urlEdit;
    public $priorityEdit;
    public $statusEdit;
    public $ticker_update;
    // DELETE
    public $ticker_selected_id_delete;
    public $ticker_name_selected_delete;
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
        'filterTickers' => 'filterTickers',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = Ticker::max('priority') + 1;
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
            $rules['tickers.' . $locale] =  [
                'required',
                'string',
                'min:1',
                Rule::unique('ticker_translations', 'name')
                    ->where('locale', $locale)
            ];
        }
        $rules['url'] = ['required'];
        $rules['priority'] = ['required'];
        $rules['status'] = ['required','in:0,1'];
        return $rules;
    }

    protected function rulesForUpdate()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['tickersEdit.' . $locale] = [
                'required',
                'string',
                'min:1',
                Rule::unique('ticker_translations', 'name')
                    ->where('locale', $locale)
            ];
        }
        $rules['urlEdit'] = ['required'];
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
    public function saveTicker () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());


        $ticker = Ticker::create([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'url' => $validatedData['url'],
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            TickerTranslation::create([
                'ticker_id' => $ticker->id,
                'locale' => $locale,
                'name' => $this->tickers[$locale],
                'slug' => Str::slug($this->tickers[$locale], '-', ''),
            ]);
        }

        $this->resetInput();
        $this->filterTickers($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Ticker Added Successfully')]);
    }
    public function editTicker(int $id) {
        $this->currentValidation = 'edit';
        // dd('you clicked me');
        $this->de = 0;
        // $a = Ticker::where('id',$id)->first();
        $ticker_edit = Ticker::find($id);
        $this->ticker_update = $ticker_edit;

        if ($ticker_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = TickerTranslation::where('ticker_id', $ticker_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->tickersEdit[$locale] = $translation->name;
                } else {
                    $this->tickersEdit[$locale] = 'Not Found';
                }
            }
            $this->urlEdit = $ticker_edit->url;
            $this->priorityEdit = $ticker_edit->priority;
            $this->statusEdit = $ticker_edit->status;
        } else {
        // error message
        }

    }
    public function updateTicker () {
        $validatedData = $this->validate($this->rulesForUpdate());

        Ticker::where('id', $this->ticker_update->id)->update([
            'created_by_id' => auth('admin')->id() ?? 1,
            'updated_by_id' => auth('admin')->id() ?? 1,
            'url' => $validatedData['urlEdit'],
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
            'image' => $this->objectName ?? $this->objectReader,
        ]);
    
        $ticker = Ticker::find($this->ticker_update->id);

        foreach ($this->filteredLocales as $locale) {
            TickerTranslation::updateOrCreate(
                [
                    'ticker_id' => $ticker->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->tickersEdit[$locale],
                    'slug' => Str::slug($this->tickersEdit[$locale], '-', ''),
                ]
            );
        }

        $this->closeModal();
        $this->filterTickers($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Ticker Updated Successfully')]);
    }
    public function removeTicker (int $id) {
        $this->ticker_selected_id_delete = Ticker::find($id);
        $this->ticker_name_selected_delete = TickerTranslation::where('ticker_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->ticker_name_selected_delete) {
            $this->showTextTemp = $this->ticker_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $tickerNameToDelete = '';
    public function destroyTicker () {
        if ($this->confirmDelete && $this->tickerNameToDelete === $this->showTextTemp) {
            Ticker::find($this->ticker_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Ticker Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->ticker_selected_id_delete = null;
            $this->ticker_name_selected_delete = null;
            $this->tickerNameToDelete = '';
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
    
        // Find the Ticker by ID
        $ticker = Ticker::find($p_id);
        
        if ($ticker) {
            $ticker->priority = $updatedPriority;
            $ticker->save();
            
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
        // Find the ticker by ID, if not found return an error
        $tickerstatus = Ticker::find($id);
    
        if ($tickerstatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $tickerstatus->status = !$tickerstatus->status;
            $tickerstatus->save();
    
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
    public function filterTickers($status)
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
            $this->tickers[$locale] = "";
            $this->tickersEdit[$locale] = "";
        }
        $this->status = 1;
        $this->url;
        $this->priority = Ticker::max('priority') + 1;
        $this->statusEdit = 1;
        $this->urlEdit = '';
        $this->priorityEdit = '';
        $this->objectReader = null;
        $this->objectName = null;
        $this->objectData = null;

        $this->emit('resetData');
        $this->emit('resetEditData');
    }


    // Render
    public function render(){
        $this->activeCount = Ticker::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = Ticker::where('status', 0)->count() ?? 0;

        $query = Ticker::with(['tickerTranslation' => function ($query) {
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
            $query->whereHas('TickerTranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10)->withQueryString(); 

        return view('super-admins.pages.tickers.ticker-table', [
            'tableData' => $tableData,
            'objectReader' => $this->objectReader
        ]);
    }
}