<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\PhenixSystem;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\Rule;

class PhenixSystemsLivewire extends Component
{
    public $systems;

    public $system_id = null;

    public $name = '';
    public $code = '';
    public $base_url = '';
    public $username = '';

    // Secrets: keep separate "inputs" so we can avoid showing stored values
    public $password = '';
    public $token = '';

    public $is_active = true;

    // UI helpers
    public $testing = false;
    public $test_result = null; // ['ok' => bool, 'message' => string]

    public function mount()
    {
        $this->loadSystems();
        $this->resetForm();
    }

    public function loadSystems(): void
    {
        $this->systems = PhenixSystem::orderBy('id', 'asc')->get();
    }

    public function resetForm(): void
    {
        $this->resetErrorBag();
        $this->resetValidation();

        $this->system_id = null;

        $this->name = '';
        $this->code = '';
        $this->base_url = '';
        $this->username = '';

        // Do NOT prefill secrets
        $this->password = '';
        $this->token = '';

        $this->is_active = true;

        $this->test_result = null;
    }

    public function createNew(): void
    {
        $this->resetForm();
    }

    public function edit(int $id): void
    {
        $system = PhenixSystem::findOrFail($id);

        $this->system_id = $system->id;
        $this->name = $system->name;
        $this->code = $system->code;
        $this->base_url = $system->base_url;
        $this->username = $system->username;
        $this->is_active = (bool) $system->is_active;

        // Do NOT load password/token into the form for security.
        $this->password = '';
        $this->token = '';

        $this->test_result = null;
    }

    protected function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => [
                'required',
                'string',
                'max:255',
                Rule::unique('phenix_systems', 'code')->ignore($this->system_id),
            ],
            'base_url' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],

            // Secrets:
            // - on create: required
            // - on edit: optional (only update if user types something)
            'password' => [$this->system_id ? 'nullable' : 'required', 'string', 'max:255'],
            'token'    => [$this->system_id ? 'nullable' : 'required', 'string', 'max:2048'],
        ];
    }

    public function saveSystem(): void
    {
        $this->validate();

        $payload = [
            'name'      => $this->name,
            'code'      => $this->code,
            'base_url'  => $this->base_url,
            'username'  => $this->username,
            'is_active' => $this->is_active ? 1 : 0,
        ];

        // Only update secrets if provided
        if (!empty($this->password)) {
            $payload['password'] = $this->password;
        }
        if (!empty($this->token)) {
            $payload['token'] = $this->token;
        }

        PhenixSystem::updateOrCreate(
            ['id' => $this->system_id],
            $payload
        );

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Phenix system saved successfully!'),
        ]);

        $this->resetForm();
        $this->loadSystems();
    }

    public function deleteSystem(int $id): void
    {
        PhenixSystem::findOrFail($id)->delete();

        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Phenix system deleted successfully!'),
        ]);

        if ($this->system_id === $id) {
            $this->resetForm();
        }

        $this->loadSystems();
    }

    public function testConnection(): void
    {
        $this->testing = true;
        $this->test_result = null;

        try {
            // We test using currently typed values if present, otherwise use DB stored values.
            $system = $this->system_id ? PhenixSystem::findOrFail($this->system_id) : null;

            $baseUrl  = rtrim($this->base_url ?: ($system?->base_url ?? ''), '/');
            $username = $this->username ?: ($system?->username ?? '');
            $password = !empty($this->password) ? $this->password : ($system?->password ?? '');
            $token    = !empty($this->token) ? $this->token : ($system?->token ?? '');

            if (!$baseUrl || !$username || !$password || !$token) {
                throw new \RuntimeException(__('Please fill Base URL, Username, Password and Token first.'));
            }

            // Use a cheap call; you already know /find/18 works.
            $resp = Http::baseUrl($baseUrl)
                ->withBasicAuth($username, $password)
                ->withHeaders(['phenixtoken' => $token])
                ->timeout(10)
                ->get('/api/rest/TPhenixApi/find/18');

            if ($resp->successful()) {
                $this->test_result = [
                    'ok' => true,
                    'message' => __('Connection OK (HTTP 200).'),
                ];
            } else {
                $this->test_result = [
                    'ok' => false,
                    'message' => __('Failed with HTTP :code. :body', [
                        'code' => $resp->status(),
                        'body' => substr((string) $resp->body(), 0, 200),
                    ]),
                ];
            }
        } catch (\Throwable $e) {
            $this->test_result = [
                'ok' => false,
                'message' => $e->getMessage(),
            ];
        } finally {
            $this->testing = false;
        }
    }

    public function render()
    {
        return view('super-admins.pages.setting.phenix.form');
    }
}
