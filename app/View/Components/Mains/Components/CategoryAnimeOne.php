<?php

namespace App\View\Components\Mains\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryAnimeOne extends Component
{
    /**
     * Create a new component instance.
     */
    public $categoiresData;

    public function __construct($categoiresData)
    {
        $this->categoiresData = $categoiresData;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.category-anime-one',[
            "categoiresData" => $this->categoiresData,
        ]);
    }
}