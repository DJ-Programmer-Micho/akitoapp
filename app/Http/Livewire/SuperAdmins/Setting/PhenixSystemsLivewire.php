<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\PhenixSystem;

class PhenixSystemsLivewire extends Component
{
    public $systems = [];

    public $system_id;
    public $name;
    public $code;
    public $base_url;
    public $username;
    public $password;
    public $is_active = true;

    public function mount()
    {
        $this->loadSystems();
    }

    public function loadSystems()
    {
        $this->systems = PhenixSystem::orderBy('id', 'asc')->get();
    }

    public function resetForm()
    {
        $this->system_id = null;
        $this->name = '';
        $this->code = '';
        $this->base_url = '';
        $this->username = '';
        $this->password = '';
        $this->is_active = true;
    }

    public function edit($id)
    {
        $system = PhenixSystem::findOrFail($id);

        $this->system_id = $system->id;
        $this->name = $system->name;
        $this->code = $system->code;
        $this->base_url = $system->base_url;
        $this->username = $system->username;
        $this->password = $system->password;
        $this->is_active = (bool) $system->is_active;
    }

    public function createNew()
    {
        $this->resetForm();
    }

    public function saveSystem()
    {
        $this->validate([
            'name'     => 'required|string|max:255',
            'code'     => 'required|string|max:255|unique:phenix_systems,code,' . $this->system_id,
            'base_url' => 'required|string|max:255',
            'username' => 'required|string|max:255',
            'password' => 'required|string|max:255',
        ]);

        PhenixSystem::updateOrCreate(
            ['id' => $this->system_id],
            [
                'name'      => $this->name,
                'code'      => $this->code,
                'base_url'  => $this->base_url,
                'username'  => $this->username,
                'password'  => $this->password,
                'is_active' => $this->is_active ? 1 : 0,
            ]
        );

        $this->dispatchBrowserEvent('alert', [
            'type'    => 'success',
            'message' => __('Phenix system saved successfully!'),
        ]);

        $this->resetForm();
        $this->loadSystems();
    }

    public function deleteSystem($id)
    {
        PhenixSystem::findOrFail($id)->delete();

        $this->dispatchBrowserEvent('alert', [
            'type'    => 'success',
            'message' => __('Phenix system deleted successfully!'),
        ]);

        // If we were editing this one, reset the form
        if ($this->system_id == $id) {
            $this->resetForm();
        }

        $this->loadSystems();
    }

    public function render()
    {
        return view('super-admins.pages.setting.phenix.form');
    }
}
