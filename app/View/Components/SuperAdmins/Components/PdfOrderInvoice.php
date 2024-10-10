<?php

namespace App\View\Components\SuperAdmins\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class PdfOrderInvoice extends Component
{
    // JS CALL EVENT
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('super-admins.components.pdf-order-invoice');
    }
}
