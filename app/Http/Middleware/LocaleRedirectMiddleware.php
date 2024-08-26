<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LocaleRedirectMiddleware
{
    public $supportedLocales = ['en', 'ar', 'ku'];

    public function handle(Request $request, Closure $next)
    {
        $segments = $request->segments();

        // Check if the first segment is a supported locale
        if (in_array($segments[0], $this->supportedLocales)) {
            return $next($request);
        }

        // If locale is not valid, redirect to the default locale
        $defaultLocale = config('app.locale'); // Default locale from config
        $newUrl = url("/$defaultLocale/" . $request->path());

        return redirect($newUrl, 301); // 301 for permanent redirect
    }
}

