<?php

namespace App\View\Components\Mains\Components\Categories;

use Closure;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class GridOne extends Component
{
    /**
     * Create a new component instance.
     */
    public $categories;
    public function __construct()
    {
        $this->categories = Cache::remember('active_categories', 60, function () {
            return Category::where('status', 1)->get();
        });  
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.categories.grid-one',[
            "categories" => $this->categories,
        ]);
    }
}