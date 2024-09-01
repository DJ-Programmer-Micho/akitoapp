<?php

namespace App\View\Components\Mains\Components\Brands;

use Closure;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class GridOne extends Component
{
    /**
     * Create a new component instance.
     */
    public $brands;
    public function __construct()
    {
        $locale = app()->getLocale();  // Get the current locale

        $this->brands = Cache::remember("active_brands_$locale", 60, function () use ($locale) {
            return Brand::with([
                'brandTranslation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                }
            ])
            ->where('status', 1)
            ->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.brands.grid-one',[
            "brands" => $this->brands,
        ]);
    }
}