<?php

namespace App\Http\Controllers\Api\V1;

use App\Models\Customer;
use Illuminate\Http\Request;
use App\Services\SinchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use App\Mail\EmailVerificationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class CustomerControllerApi extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate(
                [
                    'first_name' => 'required|string|max:255',
                    'last_name' => 'required|string|max:255',
                    'username' => 'required|string|max:255|unique:customers,username',
                    'email' => 'required|email|unique:customers,email',
                    'business_module' => 'required|in:Personal,Agency,Restaurant,Coffee Shop,Hotel,Other',
                    'phone_number' => 'required|string|max:15|unique:customer_profiles,phone_number',
                    'country' => 'required|string|max:255',
                    'city' => 'required|string|max:255',
                    'address' => 'required|string|max:255',
                    'zip_code' => 'required|string|max:10',
                    'password' => 'required|string|min:8|confirmed',
                    'password_confirmation' => 'required|string|min:8',
                ], [
                    'first_name.required' => 'First name is required.',
                    'first_name.string' => 'First name must be a valid string.',
                    'last_name.required' => 'Last name is required.',
                    'last_name.string' => 'Last name must be a valid string.',
                    'username.required' => 'Username is required.',
                    'username.unique' => 'This username is already taken.',
                    'email.required' => 'Email address is required.',
                    'email.email' => 'Enter a valid email address.',
                    'email.unique' => 'This email is already registered.',
                    'business_module.required' => 'Business module is required.',
                    'business_module.in' => 'Invalid business module selection.',
                    'phone_number.required' => 'Phone number is required.',
                    'phone_number.unique' => 'This phone number is already in use.',
                    'country.required' => 'Country is required.',
                    'city.required' => 'City is required.',
                    'address.required' => 'Address is required.',
                    'zip_code.required' => 'Zip code is required.',
                    'zip_code.max' => 'Zip code cannot be longer than 10 characters.',
                    'password.required' => 'Password is required.',
                    'password.min' => 'Password must be at least 8 characters.',
                    'password.confirmed' => 'Password confirmation does not match.',
                ] 
            );
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['errors' => $e->errors()], 422);
        }

        try {
            DB::beginTransaction();
    
            // Create Customer
            $customer = Customer::create([
                'email' => $validatedData['email'],
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
                'status' => 1,
                'phone_verify' => 0,
            ]);
    
            // Create Customer Profile
            $customer->customer_profile()->create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'business_module' => $validatedData['business_module'],
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'address' => $validatedData['address'],
                'zip_code' => $validatedData['zip_code'],
                'phone_number' => $validatedData['phone_number'],
            ]);
    
            // Generate Email OTP
            $otpCodeEmail = rand(100000, 999999);
            $customer->update(['email_otp_number' => $otpCodeEmail]);
    
            DB::commit(); // ✅ Commit changes before sending email
    
            // Send Email OTP (catch failure)
            try {
                Mail::to($customer->email)->send(new EmailVerificationMail($otpCodeEmail));
            } catch (\Exception $e) {
                return response()->json([
                    'message' => 'Customer registered successfully. However, OTP email failed to send. Please request a resend.',
                    'error' => $e->getMessage(),
                ], 201);
            }
    
            return response()->json(['message' => 'Customer registered successfully. Please verify email.'], 201);
    
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Registration failed: ' . $e->getMessage()], 500);
        }
    }
    

    /**
     * 📌 Verify Email OTP API
     */
    public function verifyEmailOTP(Request $request): JsonResponse
    {
        $customer = Customer::where('email', $request->input('email'))->first();
        
        if ($customer && $request->input('otp_code') == $customer->email_otp_number) {
            $customer->email_verify = 1;
            $customer->save();
            return response()->json(['message' => 'Email verified successfully.'], 200);
        }
        return response()->json(['message' => 'Invalid OTP.'], 400);
    }

    /**
     * 📌 Resend Email OTP API
     */
    public function resendEmailOTP(Request $request): JsonResponse
    {
        $customer = Customer::where('email', $request->input('email'))->first();
        
        if ($customer) {
            $otpCodeEmail = rand(100000, 999999);
            $customer->update(['email_otp_number' => $otpCodeEmail]);
            Mail::to($customer->email)->send(new EmailVerificationMail($otpCodeEmail));
            return response()->json(['message' => 'New OTP sent.'], 200);
        }
        return response()->json(['message' => 'Customer not found.'], 404);
    }

    /**
     * 📌 Verify Phone OTP API
     */
    public function verifyPhoneOTP(Request $request): JsonResponse
    {
        $customer = Customer::where('id', $request->input('id'))->first();
        $toNumber = $customer->customer_profile->phone_number;

        $response = SinchService::verifyOTP($toNumber, $request->input('otp_code'));

        if ($response->successful()) {
            $customer->phone_verify = 1;
            $customer->save();
            return response()->json(['message' => 'Phone number verified successfully.'], 200);
        }
        return response()->json(['message' => 'Invalid OTP.'], 400);
    }

    public function sendPhoneNumberAfterVerification(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
        ]);

        // Find the customer by email
        $customer = Customer::where('email', $request->email)->first();

        if (!$customer || !$customer->email_verify) {
            return response()->json(['message' => 'Email is not verified or customer does not exist.'], 400);
        }

        // Get the customer's phone number
        $phoneNumber = $customer->customer_profile->phone_number ?? null;

        if (!$phoneNumber) {
            return response()->json(['message' => 'Phone number not found.'], 404);
        }

        return response()->json([
            'message' => 'Phone number retrieved successfully.',
            'phone_number' => $phoneNumber,
        ], 200);
    }
    /**
     * 📌 Resend Phone OTP API
     */
    public function resendPhoneOTP(Request $request): JsonResponse
    {
        $customer = Customer::where('id', $request->input('id'))->first();
        
        if ($customer) {
            $toNumber = $customer->customer_profile->phone_number;
            $response = SinchService::sendOTP($toNumber);

            if ($response->successful()) {
                return response()->json(['message' => 'New OTP sent.'], 200);
            }
        }
        return response()->json(['message' => 'Failed to send OTP.'], 400);
    }


    public function customerLogin(Request $request): JsonResponse
    {
        $request->validate([
            'login'    => 'required|string',
            'password' => 'required|string|min:8',
        ]);

        // Determine if login is by email or username
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        $credentials = [
            $loginType  => $request->input('login'),
            'password'  => $request->input('password'),
        ];

        // Attempt login
        if (!Auth::guard('customer')->attempt($credentials)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        // Get authenticated customer
        $customer = Auth::guard('customer')->user();
        
        // Ensure customer exists
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        // Generate Sanctum Token
        $token = $customer->createToken('CustomerAuth')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'token'   => $token,
            'user'    => [
                'id'        => $customer->id,
                'username'  => $customer->username,
                'email'     => $customer->email,
            ],
        ], 200);
    }

    public function customerDetails(Request $request): JsonResponse
    {
        $customer = Auth::guard('sanctum')->user(); // Correct way to get authenticated user with Sanctum

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return response()->json([
            'data' => [
                'id'    => $customer->id,
                'username' => $customer->username,
                'email' => $customer->email,
            ],
            'verify' => [
                'emailOtp' => $customer->email_verify,
                'phoneOtp' => $customer->phone_verify,
                'companyOtp' => $customer->company_verify,
            ],
            'information' => [
                'firstName'  => $customer->customer_profile->first_name ?? 'unKnown',
                'lastName'  => $customer->customer_profile->last_name ?? 'unKnown',
                'phone_number'  => $customer->customer_profile->phone_number ?? 'unKnown',
                'businessModel'  => $customer->customer_profile->business_module ?? 'unKnown',
                'brandName'  => $customer->customer_profile->brand_name ?? 'unKnown',
            ],
        ], 200);
    }

    public function customerLogout(Request $request): JsonResponse
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $customer->tokens()->delete(); // Delete all tokens (logout from all devices)

        return response()->json([
            'message' => 'User logged out successfully',
        ], 200);
    }

    public function addAddress(Request $request): JsonResponse
    {
        $customer = Auth::guard('sanctum')->user();
        
        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        if ($customer->customer_addresses()->count() >= 5) {
            return response()->json(['message' => 'You can only have up to 5 delivery addresses.'], 400);
        }

        $validated = $request->validate([
            'type' => 'required|in:Apartment,House,Office',
            'building_name' => 'nullable|string|max:255',
            'apt_or_company' => 'nullable|string|max:255',
            'address_name' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|regex:/^\+?[0-9]{10,15}$/',
            'additional_directions' => 'nullable|string',
            'address_label' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $customer->customer_addresses()->create($validated);

        return response()->json(['message' => 'Address added successfully.'], 201);
    }

    /**
     * 📌 Edit Customer Address API
     */
    public function editAddress(Request $request, $addressId): JsonResponse
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $address = $customer->customer_addresses()->findOrFail($addressId);

        $validated = $request->validate([
            'type' => 'required|in:Apartment,House,Office',
            'building_name' => 'nullable|string|max:255',
            'apt_or_company' => 'nullable|string|max:255',
            'address_name' => 'nullable|string|max:255',
            'floor' => 'nullable|string|max:255',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|regex:/^\+?[0-9]{10,15}$/',
            'additional_directions' => 'nullable|string',
            'address_label' => 'nullable|string|max:255',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $address->update($validated);

        return response()->json(['message' => 'Address updated successfully.'], 200);
    }

    /**
     * 📌 Delete Customer Address API
     */
    public function deleteAddress($addressId): JsonResponse
    {
        $customer = Auth::guard('sanctum')->user();

        if (!$customer) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $address = $customer->customer_addresses()->findOrFail($addressId);
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully.'], 200);
    }

}
