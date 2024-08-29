<?php

namespace App\View\Components\Mains\Components\Shops;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class AsideFilterOne extends Component
{
    public $brands;
    public $categories;
    public $subCategory;
    public $sizes;
    public $colors;
    public $capacities;
    public $materials;
    public $minPrice;
    public $maxPrice;
    /**
     * Create a new component instance.
     */
    public function __construct($brands, $categories, $subCategory, $sizes, $colors, $capacities, $materials, $minPrice, $maxPrice)
    {
        $this->brands = $brands;
        $this->categories = $categories;
        $this->subCategory = $subCategory;
        $this->sizes = $sizes;
        $this->colors = $colors;
        $this->capacities = $capacities;
        $this->materials = $materials;
        $this->minPrice = $minPrice;
        $this->maxPrice = $maxPrice;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.shops.aside-filter-one',[
            "brands" => $this->brands,
            "categories" => $this->categories,
            "subCategory" => $this->subCategory,
            "sizes" => $this->sizes,
            "colors" => $this->colors,
            "capacities" => $this->capacities,
            "materials" => $this->materials,
            "minPrice" => $this->minPrice,
            "maxPrice" => $this->maxPrice
        ]);
    }
}
