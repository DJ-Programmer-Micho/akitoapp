<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Password;

class AuthController extends Controller
{
    public function signIn(){
        return view('super-admins.auth.signin-one');
    }

    public function handleSignIn(Request $request){
        // Validate input
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        // Attempt login
        // Authentication successful
        if (Auth::guard('admin')->attempt($request->only('email', 'password'))) {
            return redirect()->route('super.dashboard', ['locale' => app()->getLocale()]);
        }
        // If authentication fails
        return back()->withErrors([
            'email' => 'Invalid email or password.',
        ])->withInput();
    }
    
    public function paswwordReset(){
        return view('super-admins.auth.reset-password-one');
    }

    public function sendResetLinkEmail(Request $request){
        $request->validate(['email' => 'required|email']);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with(['status' => __($status)])
            : back()->withErrors(['email' => __($status)]);
    }

    public function lock()
    {
        // Store user data in the session before logging out
        $user = auth('admin')->user();
        if ($user) {
            $userLock = User::with('profile')->find($user->id);
    
            $avatar = optional($userLock->profile)->avatar 
                ? app('cloudfront') . $userLock->profile->avatar 
                : app('userImg'); // Use default image if no avatar exists
            
            session([
                'user_data' => [
                    'email' => $userLock->email,
                    'avatar' => $avatar,
                    'name' => optional($userLock->profile)->first_name . ' ' . optional($userLock->profile)->last_name,
                ],
                'last_page_url' => url()->previous() // Store the last page URL
            ]);
    
            // Log out the user
            session()->regenerateToken(); // Regenerate session token for security
            Auth::guard('admin')->logout();
            return view('super-admins.auth.lock-one'); // Display lock screen
    
        } else {
            return redirect()->route('super.signin')->with('alert', [
                'type' => 'error',
                'message' => 'You must be logged in to access this page.',
            ]);
        }
    }
    


    public function unlock(Request $request)
    {
        $credentials = $request->only('password');
        $userData = session('user_data'); // Retrieve stored session data
    
        // Check if session data exists
        if (!$userData) {
            return redirect()->route('super.signin'); // Redirect to login if session expired
        }
    
        // Attempt to log in with stored email and entered password
        if (Auth::attempt(['email' => $userData['email'], 'password' => $credentials['password']])) {
            session()->forget('user_data'); // Clear user data from session
            $lastPageUrl = session('last_page_url', '/home'); // Default to home if no URL stored
            session()->forget('last_page_url'); // Clear the last page URL
            return redirect()->intended($lastPageUrl); // Redirect to the last visited page
        }
    
        // Authentication failed, return error message
        return back()->withErrors([
            'password' => 'The provided password is incorrect.',
        ]);
    }
    
    public function signOut(){
        Auth::guard('admin')->logout(); // Log out the user
        return redirect()->to('/auth-logout');
    }
    public function logoutpage() {
        return view('super-admins.auth.logout-one'); // Display lock screen
    }
    public function suspend() {
        return view('super-admins.auth.suspend-one'); // Display lock screen
    }

}