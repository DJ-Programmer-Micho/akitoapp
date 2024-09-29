<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use App\Models\ShippingCost;

class ShippingCostLivewire extends Component
{
    
    public $shippingCost; 
    public $first_km_cost;
    public $additional_km_cost;
    public $free_delivery_over;

    protected $rules = [
        'first_km_cost' => 'required|numeric|min:0',
        'additional_km_cost' => 'required|numeric|min:0',
        'free_delivery_over' => 'nullable|numeric|min:0',
    ];

    public function mount()
    {
        if(ShippingCost::first()){
            $this->shippingCost = ShippingCost::first();
            $this->first_km_cost = $this->shippingCost->first_km_cost;
            $this->additional_km_cost = $this->shippingCost->additional_km_cost;
            $this->free_delivery_over = $this->shippingCost->free_delivery_over;
        }
    }

    public function shippingSubmit()
    {
        $validatedData = $this->validate();
        $this->shippingCost = ShippingCost::first();
        if ($this->shippingCost) {
            // Update existing record
            $this->shippingCost->update($validatedData);
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Shipping cost updated successfully.')]);
        } 
    }
    public function render()
    {
        return view('super-admins.pages.shippingcost.shipping-table');
    }
}