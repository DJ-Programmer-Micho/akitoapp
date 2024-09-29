<?php

namespace App\View\Components\Mains\Components\Account;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\Component;

class PasswordOne extends Component
{
    public $customer;

    public function __construct()
    {
        $this->customer = Auth::guard('customer')->user();
    }

    public function render(): View|Closure|string
    {
        return view('mains.components.account.password-one', [
            'customer_id' => $this->customer->id,
        ]);
    }
}
