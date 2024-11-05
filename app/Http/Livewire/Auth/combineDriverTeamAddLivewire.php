<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use App\Models\DriverTeam;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class combineDriverTeamAddLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['page'];
    protected $glang;
    // INT
    public $filteredLocales;
    // Create
    public $TeamList = [];
    public $deliveryList = [];
    public $selectedTeam = [];
    public $selectedDelivery = [];


    public $page = 1;

    // VALIDATION
    public $currentValidation = 'addTeam';
    // On Load
    public function mount(){
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


    protected function rulesForSaveTeamMembers()
    {
        return [
            'selectedTeam' => ['required', 'integer', 'exists:driver_teams,id'], // Ensure the selected team exists
            'selectedDelivery' => ['required', 'array', 'min:1'], // Ensure at least one driver is selected
            'selectedDelivery.*' => ['integer', 'exists:users,id'], // Ensure each selected driver exists
        ];
    }

    // Real-Time Validation
    public function updated($propertyName)
    {
        // $this->validateOnly($propertyName);
        if($this->currentValidation == 'addTeam') {
            $this->validateOnly($propertyName, $this->rulesForSaveTeamMembers());
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

    public function addDriverToTeam(){
        $this->currentValidation = 'addTeam';
        $validatedData = $this->validate($this->rulesForSaveTeamMembers());
        if(DriverTeam::find($this->selectedTeam)) {
            $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('The Team Is Exist!')]);
            return;
        }
        try {
            foreach ($this->selectedDelivery as $driverId) {
                // Check if the driver is already assigned to this team to avoid duplicates
                $existingMembership = DB::table('driver_team_membership')
                    ->where('team_id', $this->selectedTeam)
                    ->where('user_id', $driverId)
                    ->exists();
    
                if (!$existingMembership) {
                    // Insert a new record into the driver_team_membership table
                    DB::table('driver_team_membership')->insert([
                        'team_id' => $this->selectedTeam,
                        'user_id' => $driverId,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
    
            // Success message after insertion
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Drivers successfully added to the team!')]);
            redirect()->route('super.driver.team', ['locale' => app()->getLocale()]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('A-Error: ' . $e->getMessage())]);
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
        $this->selectedTeam = null;
        $this->selectedDelivery = [];

        $this->emit('resetData');
        $this->emit('resetEditData');
    }


    // Render
    public function render(){
        return view('super-admins.pages.driverteam.combine-driver-team-add');
    }
}