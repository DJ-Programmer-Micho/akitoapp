<?php

namespace App\Http\Livewire\Account;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
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

    protected function rulesForUpdate()
    {
        return [
            'fName' => ['required', 'string', 'max:255'],
            'lName' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'emailAddress' => [ // Use 'emailAddress' for consistency
                'required',
                'email',
                'max:255',
                Rule::unique('customers', 'email')->ignore(Auth::guard('customer')->id()),
            ],
            'country' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'address' => ['required', 'string', 'max:255'],
            'zipcode' => ['required', 'string', 'max:255'],
        ];
    }
    
    protected function rules()
    {
        // Intentionally left empty to be overridden by rulesForUpdate() when needed
    }
    
    public function updated($propertyName)
    {
        // Call the specific rules for update validation
        $this->validateOnly($propertyName, $this->rulesForUpdate());
    }

    public function mount(){
        if(Auth::guard('customer')->check()){
            $this->loadCustomer();
        } else {

        }
    }


    public function editInfo()
    {
        if (Auth::guard('customer')->check()) {
            $this->editable = true;
            $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('The form is in edit mode. You have 5 minutes to change your data.')]);
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

    public function updateInfo()
    {
        $customer = Auth::guard('customer')->user();
        
        if ($customer) {
            // Validate using the update rules
            $validatedData = $this->validate($this->rulesForUpdate());
    
            // Begin database transaction (optional but recommended for safety)
            DB::transaction(function () use ($customer, $validatedData) {
                // Update customer details
                $customer->update([
                    'email' => $validatedData['emailAddress'], // Consistency in field names
                ]);
    
                // Update or create the customer profile
                $customer->customer_profile()->updateOrCreate(
                    ['customer_id' => $customer->id], // Match profile by customer ID
                    [
                        'first_name' => $validatedData['fName'],
                        'last_name' => $validatedData['lName'],
                        'country' => $validatedData['country'],
                        'city' => $validatedData['city'],
                        'address' => $validatedData['address'],
                        'zip_code' => $validatedData['zipcode'],
                        'phone_number' => $validatedData['phone'],
                    ]
                );
            });
    
            // Turn off edit mode
            $this->editable = false;
    
            // Notify the user of success
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Your information has been updated successfully.')]);
        }
    }
    


    public function render()
    {
        return view('mains.components.livewire.account.detail-one', [
        ]);
    }
}
