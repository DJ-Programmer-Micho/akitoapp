<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocalizationMainMiddleware
{
    public $selectedLanguages = ['en', 'ar', 'ku'];

    public function handle(Request $request, Closure $next)
    {

        $locale = $request->route('locale') ?? $request->session()->get('applocale', config('app.locale'));

        if (in_array($locale, $this->selectedLanguages)) {
            App::setLocale($locale);
        } else {
            $locale = config('app.locale'); // Default locale if invalid
            App::setLocale($locale);
        }

        $request->session()->put('applocale', $locale);

        return $next($request);
    }

    public function setLocale(Request $request)
    {
        $selectedLocale = $request->input('locale');

        if (in_array($selectedLocale, $this->selectedLanguages)) {
            $request->session()->put('applocale', $selectedLocale);
            App::setLocale($selectedLocale);
        }

        // Redirect to the URL with the new locale
        $url = $request->session()->get('previous_url') ?: url()->previous();
        return redirect()->to($this->addLocaleToUrl($url, $selectedLocale));
    }

protected function addLocaleToUrl($url, $locale)
{
    $parsedUrl = parse_url($url);

    // Extract path and query
    $path = $parsedUrl['path'] ?? '';
    $query = isset($parsedUrl['query']) ? '?' . $parsedUrl['query'] : '';

    // Normalize the path (remove leading and trailing slashes)
    $path = trim($path, '/');

    // Check if the path starts with a locale and if it’s the only segment
    if (preg_match('/^(en|ar|ku)(\/|$)/', $path, $matches)) {
        // If the path only has a single locale segment
        if ($matches[1] === $path) {
            // Replace the existing locale prefix with the new one
            $path = $locale;
        } else {
            // Replace the existing locale prefix with the new one
            $path = preg_replace('/^(en|ar|ku)\//', "$locale/", $path);
        }
    } else {
        // Add the new locale prefix
        $path = "$locale/$path";
    }

    // Rebuild the URL with the new path and existing query parameters
    $result = $parsedUrl['scheme'] . '://' . $parsedUrl['host'] .
              (isset($parsedUrl['port']) ? ':' . $parsedUrl['port'] : '') .
              '/' . $path . $query;

    return $result;
}

    
    
}

