<?php

namespace App\View\Components\SuperAdmins\Auth;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ResetPassword extends Component
{
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('super-admins.auth.reset-password-one');
    }
}