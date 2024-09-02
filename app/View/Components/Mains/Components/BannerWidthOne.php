<?php

namespace App\View\Components\Mains\Components;

use Closure;
use App\Models\Brand;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class BannerWidthOne extends Component
{
    public $image;
    /**
     * Create a new component instance.
     */
    public function __construct($image)
    {
        $this->image = $image;
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.banner-width-one',[
            "image" => $this->image
        ]);
    }
}