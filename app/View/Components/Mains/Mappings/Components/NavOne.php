<?php

namespace App\View\Components\Mains\Mappings\Components;

use Closure;
use App\Models\Brand;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class NavOne extends Component
{
    public $brands;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->brands = Cache::remember('active_brands', 60, function () {
            return Brand::where('status', 1)->get();
        });    
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.mappings.components.nav-one',[
            "brands" => $this->brands
        ]);
    }
}
