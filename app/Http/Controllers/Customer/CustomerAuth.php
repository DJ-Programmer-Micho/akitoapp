<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;


class CustomerAuth extends Controller
{
    public function login(Request $request)
    {
        // Validate the form data
        $request->validate([
            'email-form-signin' => 'required|email',
            'password-form-signin' => 'required|string|min:6',
        ]);
    
        // Map form field names to the correct database column names
        $credentials = [
            'email' => $request->input('email-form-signin'),
            'password' => $request->input('password-form-signin'),
        ];
    
        // Attempt authentication with the mapped credentials
        if (Auth::guard('customer')->attempt($credentials)) {  // Ensure you're using the correct guard for customers
            // Authentication successful
            return redirect()->route('business.home', ['locale' => 'en']); // Adjust the route as needed
        }
    
        // If authentication fails
        return back()->withErrors([
            'email-form-signin' => 'Invalid email or password.',
        ])->withInput();
    }
    

    public function register(Request $request)
    {

        // Validate the request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:customers,email',
            'phone_number' => 'required|string|max:15|unique:customer_profiles,phone_number', // Checking uniqueness in 'customer_profiles' table
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'password' => 'required|string|min:8|confirmed', // 'password' and 'password_confirmation' should match
            // 'profile_picture_data' => 'nullable|string', // This will be your base64 encoded image
        ]);

        // Create new customer in the database
        $customer = Customer::create([
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'status' => 1, // Default status
            'phone_verify' => 1, // OTP non-functional for now
            // 'phone_otp_number' and 'phone_verified_at' are left for future implementation
        ]);

        // Create customer profile
        $customer->customer_profile()->create([
            'first_name' => $validatedData['first_name'],
            'last_name' => $validatedData['last_name'],
            'country' => $validatedData['country'],
            'city' => $validatedData['city'],
            'address' => $validatedData['address'],
            'zip_code' => $validatedData['zip_code'],
            'phone_number' => $validatedData['phone_number'], // Store phone number in profile too
            'avatar' => null, // Store profile picture path in AWS S3 if uploaded
        ]);

        // Store profile picture in AWS S3 if provided
        // $profilePicturePath = null;
        // if ($request->hasFile('profile_picture')) {
        //     $firstName = $validatedData['first_name'] ?? 'user';
        //     $lastName = $validatedData['last_name'] ?? 'user';
        //     $microtime = str_replace('.', '', microtime(true));
        //     $fileName = $firstName . '_' . $lastName . '_user_' . date('Ymd') . $microtime . '.' . $request->file('profile_picture')->getClientOriginalExtension();
    
        //     $profilePicturePath = Storage::disk('s3')->put(
        //         'customer/customer/' . $fileName,
        //         file_get_contents($request->file('profile_picture')->getRealPath()),
        //         'public'
        //     );
        // }
    
    
        // Store customer in Firebase Authentication
        $firebase = (new Factory)->withServiceAccount(base_path('resources/credentials/firebase_credentials.json')); // Path to your Firebase credentials
        $auth = $firebase->createAuth();
        $firebaseUser = $auth->createUser([
            'email' => $validatedData['email'],
            'password' => $validatedData['password'],
        ]);
    
        // Redirect or return success message
        return redirect()->route('business.account', ['locale' => app()->getLocale()]);
    }
    public function logout(){
        Auth::guard('customer')->logout();
        return redirect()->route('business.home', ['locale' => app()->getLocale()]);
    }
    public function avatarupload(Request $request)
    {
        // Validate the image
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);
    
        // Handle file upload
        $file = $request->file('profile_picture');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $filename);
    
        // Respond with file URL or success message
        return response()->json(['url' => asset('uploads/' . $filename)]);
    }

    public function updatePassword()
    {
        $customer = Auth::guard('customer');
    
        // Validation logic
        $data = request()->validate([
            'old_password' => 'required',
            'new_password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'confirm_password' => 'required|same:new_password',
        ]);
    
        // Check if old password matches
        if (!Hash::check($data['old_password'], $customer->user()->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect.']);
        }
    
        // Check if old and new passwords are the same
        if ($data['old_password'] === $data['new_password']) {
            return back()->withErrors(['new_password' => 'New password cannot be the same as the old password.']);
        }
    
        // Update password
        $customer->user()->update([
            'password' => Hash::make($data['new_password']),
        ]);
    
        return redirect()->back(); // Redirect after updating the password
    }
    
    
}
