<?php

namespace App\View\Components\Mains\Components\Account;

use Closure;
use Illuminate\View\Component;
use App\Models\CustomerAddress;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use App\Models\Customer; // Make sure to import the Customer model

class AddressOne extends Component
{
    public $customerAddress;

    public function __construct()
    {
        // Fetch the customer's address, or an empty collection if not found
        $this->customerAddress = CustomerAddress::where('customer_id', Auth::guard('customer')->user()->id)->get();
    }

    public function render(): View|Closure|string
    {
        return view('mains.components.account.address-one', [
            'addresses' => $this->customerAddress
        ]);
    }
}
