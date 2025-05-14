<?php

namespace App\View\Components\Mains\Components\Soons;

use Closure;
use App\Models\ComingSoon;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class GridOne extends Component
{
    /**
     * Create a new component instance.
     */
    public $soons;
    public function __construct()
    {
        $locale = app()->getLocale();  // Get the current locale

        $this->soons = Cache::remember("active_coming_soon_$locale", 60, function () use ($locale) {
            return ComingSoon::with([
                'coming_soon_translation' => function ($query) use ($locale) {
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
        return view('mains.components.soons.grid-one',[
            "soons" => $this->soons,
        ]);
    }
}