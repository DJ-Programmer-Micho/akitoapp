<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use App\Models\Product;

class UpdateProductSlug
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $locale = $request->route('locale');
        $slug = $request->route('slug');
    
        // Set the application locale
        App::setLocale($locale);
        
        // Fetch the product's translation for the given locale
        $product = Product::with(['productTranslation' => function ($query) use ($locale) {
            $query->where('locale', $locale);
        }])->whereHas('productTranslation', function ($query) use ($locale) {
            $query->where('locale', $locale);
        })->first();
    
        // Debug the fetched product and its translation
        // dd($locale, $slug, $product);
        
        if ($product && $product->productTranslation) {
            // Get the slug for the locale
            $currentSlug = $product->productTranslation->slug;
            
            // Debug the current slug
            // dd($currentSlug);

            if ($currentSlug !== $slug) {
                // Redirect to the correct URL with the updated slug
                return redirect()->route('business.productDetail', [
                    'locale' => $locale,
                    'slug' => $currentSlug
                ]);
            }
        }
    
        return $next($request);
    }
}
