<?php

namespace App\View\Components\Mains\Components\Faq;

use Closure;
use App\Models\Brand;
use App\Models\Category;
use Illuminate\View\Component;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Cache;

class CtaOne extends Component
{
    /**
     * Create a new component instance.
     */
    public $bgImg;

    public function __construct()
    {
        $this->bgImg = 'https://images.pexels.com/photos/1695052/pexels-photo-1695052.jpeg?auto=compress&cs=tinysrgb&w=1260&h=750&dpr=1';
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('mains.components.faq.cta-one',[
            "bgImg" => $this->bgImg,
        ]);
    }
}