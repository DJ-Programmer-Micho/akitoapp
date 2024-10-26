<?php

namespace App\View\Components\Mains\Mappings\Components;

use Closure;
use App\Models\Ticker;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class TickerOne extends Component
{
    public $tickers;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->tickers = Cache::remember('active_tickers', 60, function () {
            $locale = app()->getLocale();
            return Ticker::where('status', 1)
            ->with(['tickerTranslation' => function ($query) use ($locale) {
                $query->where('locale', $locale);
            }])
            ->get();
        }); 
    }
    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.mappings.components.ticker-one',[
            'tickers' => $this->tickers
        ]);
    }
}