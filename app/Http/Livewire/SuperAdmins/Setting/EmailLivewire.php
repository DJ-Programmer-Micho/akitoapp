<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Hash;

class EmailLivewire extends Component
{
    public $email_mailer;
    public $email_host;
    public $email_port;
    public $email_username;
    public $email_password;
    public $email_encryption;
    public $email_from_address;
    public $email_from_name;

    public function mount()
    {
        // Load existing email settings from the database
        $settings = WebSetting::find(1);

        if ($settings) {
            $this->email_mailer = $settings->email_mailer;
            $this->email_host = $settings->email_host;
            $this->email_port = $settings->email_port;
            $this->email_username = $settings->email_username;
            $this->email_password = $settings->email_password; // Store securely in production
            $this->email_encryption = $settings->email_encryption;
            $this->email_from_address = $settings->email_from_address;
            $this->email_from_name = $settings->email_from_name;
        }
    }

    public function save()
    {
        $this->validate([
            'email_mailer' => 'required|string',
            'email_host' => 'required|string',
            'email_port' => 'required|numeric',
            'email_username' => 'required|string',
            'email_password' => 'required|string',
            'email_encryption' => 'nullable|string|in:ssl,tls',
            'email_from_address' => 'required|email',
            'email_from_name' => 'required|string',
        ]);

        // Save or update email settings
        WebSetting::updateOrCreate(
            ['id' => 1],
            [
                'email_mailer' => $this->email_mailer,
                'email_host' => $this->email_host,
                'email_port' => $this->email_port,
                'email_username' => $this->email_username,
                'email_password' => $this->email_password,
                'email_encryption' => $this->email_encryption,
                'email_from_address' => $this->email_from_address,
                'email_from_name' => $this->email_from_name,
            ]
        );

        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Information Has Been Updated')]);
    }

    public function render()
    {
        return view('super-admins.pages.setting.email.form');
    }
}
