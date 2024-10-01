<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class LocalizationMainMiddleware
{
    public $selectedLanguages = ['en', 'ar', 'ku'];

    public function handle(Request $request, Closure $next)
    {
        // Retrieve locale from route or session, fallback to default
        $locale = $request->route('locale') ?? $request->session()->get('applocale', config('app.locale'));

        if (in_array($locale, $this->selectedLanguages)) {
            App::setLocale($locale);
        } else {
            $locale = config('app.locale'); // Default locale if invalid
            App::setLocale($locale);
        }

        // Store the current locale in the session
        $request->session()->put('applocale', $locale);

        return $next($request);
    }

    public function setLocale(Request $request)
    {
        // Get the selected locale from the request
        $selectedLocale = $request->input('locale');

        // Check if the selected locale is valid
        if (in_array($selectedLocale, $this->selectedLanguages)) {
            // Store the new locale in the session
            $request->session()->put('applocale', $selectedLocale);
            App::setLocale($selectedLocale);
        }

        // Get the previous URL to redirect
        $url = $request->session()->get('previous_url') ?: url()->previous();

        // Check if the URL is the root URL (e.g., /en)
        if (preg_match('/^\/(en|ar|ku)(\/)?$/', $url)) {
            $url = $this->addLocaleToUrl(url('/'), $selectedLocale);
        } else {
            $url = $this->addLocaleToUrl($url, $selectedLocale);
        }

        // Redirect to the URL with the new locale
        return redirect()->to($url);
    }

    protected function addLocaleToUrl($url, $locale)
    {
        $parsedUrl = parse_url($url);

        // Extract path and query
        $path = $parsedUrl['path'] ?? '';
        $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';

        // Normalize the path (remove leading and trailing slashes)
        $path = trim($path, '/');

        if ($path == 'en' || $path == 'ar' || $path == 'ku') {
            // If it's the root URL, return with the new locale
            return url("/$locale");
        }

        // Check if the path matches the product URL pattern (e.g., /en/product/{slug})
        if (preg_match('/^(en|ar|ku)(\/product\/)(.+)/', $path, $matches)) {
            // Extract the current slug and locale from the URL
            $currentSlug = urldecode($matches[3]); // Decode the slug
            $currentLocale = $matches[1];

            // Try to find the product translation for the current slug and locale
            $productTranslation = \App\Models\ProductTranslation::where('slug', $currentSlug)
                ->where('locale', $currentLocale)
                ->first();

            if ($productTranslation) {
                // Now, look for the translated slug in the new locale
                $newTranslation = \App\Models\ProductTranslation::where('product_id', $productTranslation->product_id)
                    ->where('locale', $locale)
                    ->first();

                // Use the new slug if it exists, or fall back to the original slug if translation is not available
                $newSlug = $newTranslation ? $newTranslation->slug : $currentSlug;

                // Construct the new path with the updated locale and slug
                $path = "$locale/product/$newSlug";
            } else {
                // If no translation is found, fall back to the original slug
                $path = "$locale/product/$currentSlug";
            }
        } else {
            // If the URL is not for a product, simply replace the locale in the URL
            $path = preg_replace('/^(en|ar|ku)\//', "$locale/", $path);
        }

        // Rebuild the URL with the updated path and query parameters
        $result = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] .
                  (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') .
                  '/' . $path . $query;

        return $result;
    }
}
