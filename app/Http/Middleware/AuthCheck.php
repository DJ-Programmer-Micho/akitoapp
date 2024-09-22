<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

class AuthCheck
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('admin')->check()) {


            return $next($request);
        } else {
            // if (!session()->has('user_data')) {
            //     // If the session has expired or 'user_data' is missing, redirect to the sign-in page
            //     return redirect('/signin');
            // }
            
            return redirect('/signin');
        }
    }
}
