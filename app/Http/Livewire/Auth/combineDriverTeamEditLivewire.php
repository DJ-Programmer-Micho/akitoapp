<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use App\Models\DriverTeam;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;

class combineDriverTeamEditLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['page'];
    protected $glang;
    // INT
    public $filteredLocales;
    // Create
    public $teamName;
    public $deliveryList = [];
    public $selectedTeam;
    public $selectedDelivery = [];


    public $page = 1;

    // VALIDATION
    public $currentValidation = 'editTeam';
    // On Load
    public function mount($d_id){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        $this->getList();

        // Load team data for editing
        $team = DriverTeam::find($d_id);

        if ($team) {
            $this->selectedTeam = $team->id;
            $this->teamName = $team->team_name;
            // Load existing drivers for the team
            $this->selectedDelivery = DB::table('driver_team_membership')
                ->where('team_id', $this->selectedTeam)
                ->pluck('user_id')
                ->toArray();
        }

        $this->page = request()->query('page', 1);
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
        if($this->currentValidation == 'editTeam') {
            $this->validateOnly($propertyName, $this->rulesForSaveTeamMembers());
        }
    }

    public function getList()
    {
        $this->deliveryList = User::with('profile') // Eager load the profile relationship
            ->where('status', 1)
            ->whereHas('roles', function ($query) {
                $query->where('roles.id', 8);
            })
            ->get(['id']); // Select only the id field
    }

    public function updateDriverInTeam(){

        $this->currentValidation = 'editTeam';
        $validatedData = $this->validate($this->rulesForSaveTeamMembers());

        try {
            // Remove all existing drivers from the team
            DB::table('driver_team_membership')->where('team_id', $this->selectedTeam)->delete();

            // Add the selected drivers back to the team
            foreach ($this->selectedDelivery as $driverId) {
                DB::table('driver_team_membership')->insert([
                    'team_id' => $this->selectedTeam,
                    'user_id' => $driverId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }

            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Team members updated successfully!')]);
            redirect()->route('super.driver.team', ['locale' => app()->getLocale()]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Error: ' . $e->getMessage())]);
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
        return view('super-admins.pages.driverteam.combine-driver-team-edit');
    }
}