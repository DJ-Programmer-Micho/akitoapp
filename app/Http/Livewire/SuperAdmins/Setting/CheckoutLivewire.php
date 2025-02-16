<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\WebSetting;

class CheckoutLivewire extends Component
{
    public $free_delivery;
    public $exchange_price;

    public function mount()
    {
        $settings = WebSetting::find(1);

        if ($settings) {
            $this->free_delivery = $settings->free_delivery;
            $this->exchange_price = $settings->exchange_price;
        }
    }

    public function saveSettings()
    {
        $this->validate([
            'free_delivery' => 'nullable|integer',
            'exchange_price' => 'nullable|integer',
        ]);

        WebSetting::updateOrCreate(['id' => 1], [
            'free_delivery' => $this->free_delivery,
            'exchange_price' => $this->exchange_price,
        ]);

        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Settings updated successfully!')]);
    }

    public function render()
    {
        return view('super-admins.pages.setting.checkout.form');
    }
}
