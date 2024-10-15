<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use App\Models\DriverTeam;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;

class DriverTeamLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['page'];
    protected $glang;
    // INT
    public $filteredLocales;
    public $userImg;
    // Create
    public $teamName;
    public $TeamList = [];
    public $deliveryList = [];
    public $selectedTeam = [];
    public $selectedDelivery = [];
    // DELETE
    public $brand_selected_id_delete;
    public $brand_name_selected_delete;
    public $showTextTemp;
    public $confirmDelete;
    // Render
    public $searchTerm = '';
    public $page = 1;

    // VALIDATION
    public $currentValidation = 'addTeam';
    //LISTENERS
    protected $listeners = [
        'imgCrop' => 'handleCroppedImage',
        'filterUsers' => 'filterUsers',
    ];

    // On Load
    public function mount(){
        $this->userImg = app('userImg');
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
        $this->page = request()->query('page', 1);
        $this->getList();
    }

    // VALIDATION
    protected function rules()
    {
        //Keep It Empty
    }

    protected function rulesForSaveTeam()
    {
        return [
            'teamName' => ['required', 'string', 'max:255'],
        ];
    }
    protected function rulesForSaveTeamMembers()
    {
        return [
            'selectedTeam' => ['required', 'string', 'max:255'],
            'selectedDrivers' => ['required', 'string', 'max:255'],
        ];
    }

    protected function rulesForUpdate()
    {
        $userId = $this->user_update->id;
        
        return [
            'fNameEdit' => ['required', 'string', 'max:255'],
            'lNameEdit' => ['required', 'string', 'max:255'],
            'usernameEdit' => ['required', 'string', 'max:255'],
            'phoneEdit' => ['required', 'string', 'max:255'],
            'positionEdit' => ['required', 'string'],
            'emailEdit' => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($userId) // Ignore current user's ID for uniqueness check
            ],
            'rolesEdit' => ['required', 'array'],  // Ensure roles is an array (multiple roles)
            'statusEdit' => ['required', 'in:0,1'],  // Status validation
        ];
    }

    // Real-Time Validation
    public function updated($propertyName)
    {
        // $this->validateOnly($propertyName);
        if($this->currentValidation == 'addTeam') {
            $this->validateOnly($propertyName, $this->rulesForSaveTeam());
        } else {
            $this->validateOnly($propertyName, $this->rulesForUpdate());
        }
    }

    public function getList()
    {
        $this->TeamList = DriverTeam::where('status', 1)->get();
        
        $this->deliveryList = User::with('profile') // Eager load the profile relationship
            ->where('status', 1)
            ->whereHas('roles', function ($query) {
                $query->where('roles.id', 8);
            })
            ->get(['id']); // Select only the id field
    }

    public function addTeam(){
        $this->currentValidation = 'addTeam';
        $validatedData = $this->validate($this->rulesForSaveTeam());
        try {
            DriverTeam::create([
                'team_name' => $validatedData['teamName'],
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('A-Error: ' . $e->getMessage())]);
        }
    }
    
    public $edit_team;
    public function editTeam(int $team_id){
        $this->edit_team = DriverTeam::find($team_id);
        if($this->edit_team) {
            $this->teamName = $this->edit_team->team_name;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('No Record Found')]);

        }
    }
    
    public function updateTeam(){
        $this->currentValidation = 'addTeam';
        $validatedData = $this->validate($this->rulesForSaveTeam());
        try {
            DriverTeam::where('id', $this->edit_team->id)->update([
                'team_name' => $validatedData['teamName'],
                'status' => 1,
            ]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('A-Error: ' . $e->getMessage())]);
        }
    }
    
    public function updateStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $brandStatus = User::find($id);
    
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

    // RESET BUTTON
    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
    }
 
    public function resetInput()
    {

        $this->teamName = null;
        $this->selectedTeam = null;
        $this->selectedDelivery = [];

        $this->emit('resetData');
        $this->emit('resetEditData');
    }


    // Render
    public function render()
    {        
        $query = DriverTeam::with(['userDriverTeam.profile', 'userDriverTeam.roles']);
    
        // Optional: Apply search filter if needed
        if (!empty($this->searchTerm)) { // Change $this->search to $this->searchTerm
            $query->where('team_name', 'like', '%' . $this->searchTerm . '%')
                  ->orWhereHas('userDriverTeam.profile', function ($query) {
                      $query->where('first_name', 'like', '%' . $this->searchTerm . '%')
                            ->orWhere('last_name', 'like', '%' . $this->searchTerm . '%');
                  });
        }
    
        $tableData = $query->orderBy('created_at', 'DESC')->paginate(10);
    
        return view('super-admins.pages.driverteam.driver-team-table', [
            'tableData' => $tableData,
        ]);
    }
}