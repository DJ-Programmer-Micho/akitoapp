<?php

namespace App\View\Components\Mains\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CategoryProductOne extends Component
{
    /**
     * Create a new component instance.
     */
    public $productsData;
    public $title;
    public function __construct($productsData, $title)
    {
        $this->productsData = $productsData;
        $this->title = $title;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.category-product-one',[
            "productsData" => $this->productsData,
            "title" => $this->title
        ]);
    }
}