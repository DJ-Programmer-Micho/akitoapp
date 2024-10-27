<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckCustomerStatus
{
    public function handle($request, Closure $next)
    {
        if (Auth::guard('customer')->check()) {
            $customer = Auth::guard('customer')->user();

            // If the account is suspended, log out and redirect
            if ($customer->status == 0) {
                Auth::guard('customer')->logout();
                return redirect('/suspended-account')->with('alert', 'This account has been suspended.');
            }

            // Check for email verification
            if (is_null($customer->email_verify) || $customer->email_verify == 0) {
                return redirect()->route('goEmailOTP', [
                    'locale' => app()->getLocale(),
                    'id' => $customer->id,
                    'email' => $customer->email
                ]);
            }

            // Check for phone verification
            if (is_null($customer->phone_verify) || $customer->phone_verify == 0) {
                return redirect()->route('goOTP', [
                    'locale' => app()->getLocale(),
                    'id' => $customer->id,
                    'phone' => optional($customer->customer_profile)->phone_number
                ]);
            }

            return $next($request);
        } else {
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }
    }
}
