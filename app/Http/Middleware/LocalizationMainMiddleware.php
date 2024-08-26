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

        // Ensure the URL does not already have a locale prefix
        if (preg_match('/\/(en|ar|ku)\//', $parsedUrl['path'] ?? '', $matches)) {
            $url = str_replace($matches[0], "/$locale/", $url);
        } else {
            $url = preg_replace('/^(\/.*?)(\/.*)?$/', "/$locale$1$2", $url);
        }

        return $url;
    }
}

