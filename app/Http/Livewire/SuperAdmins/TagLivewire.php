<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Tag;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\TagTranslation;
use Illuminate\Support\Facades\Storage;

class TagLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $tags = [];
    public $slugs = [];
    public $priority;
    public $status;
    public $glang;
    // EDIT
    public $tagsEdit = [];
    public $slugsEdit = [];
    public $priorityEdit;
    public $statusEdit;
    public $tag_update;
    // DELETE
    public $tag_selected_id_delete;
    public $tag_name_selected_delete;
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
        'filterTags' => 'filterTags',
    ];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->priority = Tag::max('priority') + 1;
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
            $rules['tags.' . $locale] = 'required|string|min:1';
        }
        $rules['priority'] = ['required'];
        $rules['status'] = ['required','in:0,1'];
        return $rules;
    }

    protected function rulesForUpdate()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['tagsEdit.' . $locale] = 'required|string|min:1';
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
    public function saveTag () {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());

        $tag = Tag::create([
            'created_by_id' => 1,
            'updated_by_id' => 1,
            'priority' => $validatedData['priority'],
            'status' => $validatedData['status'],
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            TagTranslation::create([
                'tag_id' => $tag->id,
                'locale' => $locale,
                'name' => $this->tags[$locale],
            ]);
        }

        $this->closeModal();
        $this->filterTags($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('New Tag Added Successfully')]);
    }

    public function editTag(int $id) {
        $this->currentValidation = 'edit';

        $tag_edit = Tag::find($id);
        $this->tag_update = $tag_edit;

        if ($tag_edit) {
            foreach ($this->filteredLocales as $locale) {
                $translation = TagTranslation::where('tag_id', $tag_edit->id)
                    ->where('locale', $locale)
                    ->first();

                if ($translation) {
                    $this->tagsEdit[$locale] = $translation->name;
                } else {
                    $this->tagsEdit[$locale] = 'Not Found';
                }
            }
            $this->priorityEdit = $tag_edit->priority;
            $this->statusEdit = $tag_edit->status;

        } else {
        // error message
        }

    }
    public function updateTag () {
        $validatedData = $this->validate($this->rulesForUpdate());

        Tag::where('id', $this->tag_update->id)->update([
            'created_by_id' => 1,
            'updated_by_id' => 1,
            'priority' => $validatedData['priorityEdit'],
            'status' => $validatedData['statusEdit'],
        ]);
    
        $tag = Tag::find($this->tag_update->id);

        foreach ($this->filteredLocales as $locale) {
            TagTranslation::updateOrCreate(
                [
                    'tag_id' => $tag->id, 
                    'locale' => $locale
                ],
                [
                    'name' => $this->tagsEdit[$locale],
                ]
            );
        }

        $this->closeModal();
        $this->filterTags($this->statusFilter);
        $this->render();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Tag Updated Successfully')]);
    }

    public function removeTag(int $id) {
        $this->tag_selected_id_delete = Tag::find($id);
        $this->tag_name_selected_delete = TagTranslation::where('tag_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
        if ($this->tag_name_selected_delete) {
            $this->showTextTemp = $this->tag_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $tagNameToDelete = '';
    public function destroyTag () {
        if ($this->confirmDelete && $this->tagNameToDelete === $this->showTextTemp) {

            Tag::find($this->tag_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Tag Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->tag_selected_id_delete = null;
            $this->tag_name_selected_delete = null;
            $this->tagNameToDelete = '';
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
    
        // Find the tag by ID
        $tag = Tag::find($p_id);
        
        if ($tag) {
            $tag->priority = $updatedPriority;
            $tag->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Tag not found')
            ]);
        }
    }

    public function updateStatus(int $id)
    {
        // Find the tag by ID, if not found return an error
        $tagStatus = Tag::find($id);
    
        if ($tagStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $tagStatus->status = !$tagStatus->status;
            $tagStatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Tag Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Tag not found')
            ]);
        }
    }

    // CRUD Handler
    public function filterTags($status)
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
            $this->tags[$locale] = "";
            $this->tagsEdit[$locale] = "";
        }
        $this->status = 1;
        $this->priority = Tag::max('priority') + 1;
        $this->statusEdit = 1;
        $this->priorityEdit = '';
    }

    // Render
    public function render(){
        $this->activeCount = Tag::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = Tag::where('status', 0)->count() ?? 0;

        $query = Tag::with(['tagtranslation' => function ($query) {
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
            $query->whereHas('tagtranslation', function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%');
            });
        }
    
        $tableData = $query->orderBy('priority', 'ASC')->paginate(10); 

        return view('super-admins.pages.tags.tag-table', [
            'tableData' => $tableData,
        ]);
    }
}