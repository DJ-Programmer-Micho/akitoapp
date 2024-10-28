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
        $locale = app()->getLocale();  // Get the current locale
    
        $this->categories = Cache::remember("active_categories_$locale", 60, function () use ($locale) {
            return Category::with([
                'categoryTranslation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                }
            ])
            ->where('status', 1)
            ->orderBy('priority', 'ASC')
            ->get();
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