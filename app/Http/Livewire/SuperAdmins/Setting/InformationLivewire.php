<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\WebSetting;

class InformationLivewire extends Component
{
    public $logo_image;
    public $app_icon;
    public $email_address;
    public $phone_number;
    public $address;
    public $working_days;
    public $working_time;

    public $facebook_url;
    public $instagram_url;
    public $tiktok_url;

    public function mount()
    {
        $settings = WebSetting::find(1);

        if ($settings) {
            $this->logo_image = $settings->logo_image;
            $this->app_icon = $settings->app_icon;
            $this->email_address = $settings->email_address;
            $this->phone_number = $settings->phone_number;
            $this->address = $settings->address;
            $this->working_days = $settings->working_days;
            $this->working_time = $settings->working_time;
            $this->facebook_url = $settings->facebook_url;
            $this->instagram_url = $settings->instagram_url;
            $this->tiktok_url = $settings->tiktok_url;
        }
    }

    public function saveSettings()
    {
        $this->validate([
            'email_address' => 'nullable|email',
            'phone_number' => 'nullable|string|max:20',
            'address' => 'nullable|string|max:255',
            'working_days' => 'nullable|string|max:50',
            'working_time' => 'nullable|string|max:50',
            'facebook_url' => 'nullable|string|max:50',
            'instagram_url' => 'nullable|string|max:50',
            'tiktok_url' => 'nullable|string|max:50',
        ]);

        WebSetting::updateOrCreate(['id' => 1], [
            'logo_image' => $this->logo_image,
            'app_icon' => $this->app_icon,
            'email_address' => $this->email_address,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'working_days' => $this->working_days,
            'working_time' => $this->working_time,
            'facebook_url' => $this->facebook_url,
            'instagram_url' => $this->instagram_url,
            'tiktok_url' => $this->tiktok_url,
        ]);

        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Settings updated successfully!')]);
    }

    public function render()
    {
        return view('super-admins.pages.setting.information.form');
    }
}
