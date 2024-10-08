<?php

namespace App\View\Components\Mains\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class IntroSectionOne extends Component
{
    public $sliders;
    /**
     * Create a new component instance.
     */
    public function __construct($sliders)
    {
        $this->sliders = $sliders;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.intro-section-one',[
            "sliders" => $this->sliders
        ]);
    }
}