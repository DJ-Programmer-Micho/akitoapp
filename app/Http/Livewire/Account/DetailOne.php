<?php

namespace App\Http\Livewire\Account;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Facades\Auth;

class DetailOne extends Component
{
    public $fName;
    public $lName;
    public $emailAddress;
    public $country;
    public $city;
    public $address;
    public $phone;
    public $zipcode;

    public $editable = false;

    public function mount(){
        if(Auth::guard('customer')->check()){
            $this->fName = 'name-sdf';
            $this->loadCustomer();
        } else {

        }
    }
    
    private function loadCustomer(){
        $customer = Auth::guard('customer')->user();
        if($customer) {
            $customerData = Customer::with('customer_profile')->where('id', $customer->id)->first();

            $this->fName = $customerData->customer_profile->first_name;
            $this->lName = $customerData->customer_profile->last_name;
            $this->emailAddress = $customerData->email;
            $this->country = $customerData->customer_profile->country;
            $this->city = $customerData->customer_profile->city;
            $this->address = $customerData->customer_profile->address;
            $this->zipcode = $customerData->customer_profile->zip_code;
            $this->phone = $customerData->customer_profile->phone_number;
        } else {

        }
    }
   
    public function render()
    {
        return view('mains.components.livewire.account.detail-one', [
        ]);
    }
}
