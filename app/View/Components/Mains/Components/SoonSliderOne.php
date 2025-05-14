<?php

namespace App\View\Components\Mains\Components;

use Closure;
use App\Models\Brand;
use App\Models\ComingSoon;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class SoonSliderOne extends Component
{
    public $soons;
    /**
     * Create a new component instance.
     */
    public function __construct($soons)
    {
        $this->soons = $soons;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.soon-slider-one',[
            "soons" => $this->soons
        ]);
    }
}