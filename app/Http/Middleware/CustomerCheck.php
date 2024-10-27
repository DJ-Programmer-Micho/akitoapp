<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CustomerCheck
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('customer')->check()) {
            return $next($request);
        } else {
            // Uncomment this if a session check is needed
            // if (!session()->has('user_data')) {
            //     return redirect('/signin');
            // }
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }
    }
}
