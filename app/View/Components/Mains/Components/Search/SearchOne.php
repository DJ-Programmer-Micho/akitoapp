<?php

namespace App\View\Components\Mains\Components\Search;

use Closure;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class SearchOne extends Component
{
    /**
     * Create a new component instance.
     */

    public $products;
    public $searchQuery;
    public function __construct($products, $searchQuery)
    {
        $this->products = $products;
        $this->searchQuery = $searchQuery;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.search.search-one',[
            'products' => $this->products,
            'searchQuery' => $this->searchQuery,
        ]);
    }
}