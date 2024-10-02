<?php

namespace App\View\Components\Mains\Components\Faq;

use Closure;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class FaqOne extends Component
{
    /**
     * Create a new component instance.
     */
    public $brands;
    public $mainImg;
    public $aboutImg1;
    public $aboutImg2;

    public function __construct()
    {
        $locale = app()->getLocale();  // Get the current locale

        $this->mainImg = 'https://images.pexels.com/photos/9809812/pexels-photo-9809812.jpeg';
        $this->aboutImg1 = 'https://images.pexels.com/photos/1752804/pexels-photo-1752804.jpeg?auto=compress&cs=tinysrgb&w=600';
        $this->aboutImg2 = 'https://images.pexels.com/photos/414630/pexels-photo-414630.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1';
        $this->brands = Cache::remember("active_brands_$locale", 60, function () use ($locale) {
            return Brand::with([
                'brandTranslation' => function ($query) use ($locale) {
                    $query->where('locale', $locale);
                }
            ])
            ->where('status', 1)
            ->take(9)
            ->get();
        });
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.faq.faq-one',[
            "brands" => $this->brands,
            "mainImg" => $this->mainImg,
            "aboutImg1" => $this->aboutImg1,
            "aboutImg2" => $this->aboutImg2,
        ]);
    }
}