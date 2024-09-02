<?php

namespace App\View\Components\Mains\Mappings;

use Closure;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;
use Illuminate\Support\Facades\Cache;

class HeaderOne extends Component
{
    public $brands;
    public $categories;
    /**
     * Create a new component instance.
     */
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
            ->orderBy('priority', 'asc')
            ->get();
        });
        $this->categories = Cache::remember("active_categories_$locale", 60, function () use ($locale) {
            return Category::with([
                'categoryTranslation' => function ($query) use ($locale) {
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
        return view('mains.mappings.header-one',[
            "brands" => $this->brands,
            "categories" => $this->categories
        ]);
    }
}
