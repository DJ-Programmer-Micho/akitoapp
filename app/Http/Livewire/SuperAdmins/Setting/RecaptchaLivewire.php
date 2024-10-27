<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\WebSetting;

class RecaptchaLivewire extends Component
{
    public $g_key;
    public $g_secret;

    public function mount()
    {
        $settings = WebSetting::find(1);

        if ($settings) {
            $this->g_key = $settings->google_recaptcha_key;
            $this->g_secret = $settings->google_recaptcha_secret;
        }
    }

    public function saveSettings()
    {
        $this->validate([
            'g_key' => 'nullable|string',
            'g_secret' => 'nullable|string',
        ]);

        WebSetting::updateOrCreate(['id' => 1], [
            'google_recaptcha_key' => $this->g_key,
            'google_recaptcha_secret' => $this->g_secret,
        ]);

        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Settings updated successfully!')]);
    }

    public function render()
    {
        return view('super-admins.pages.setting.recaptcha.form');
    }
}
