<?php

namespace App\View\Components\Mains\Mappings;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderOne extends Component
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
        return view('mains.mappings.header-one');
    }
}
