<?php

namespace App\View\Components\Mains\Components\Register;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class ImageHeaderOne extends Component
{
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
        return view('mains.components.register.image-header-one',[

        ]);
    }
}