<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use App\Services\SinchService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Password;

use Illuminate\Support\Facades\Notification;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\Telegram\TeleNotifyCustomerNew;

class CustomerAuth extends Controller
{
    public $objectData;
    public $objectName;

    public function login(Request $request)
    {
        $request->validate([
            'login' => 'required|string',
            'password-form-signin' => 'required|string|min:8',
            'g-recaptcha-response' => ['required', new ReCaptcha],
        ]);

        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password-form-signin'),
        ];

        if (Auth::guard('customer')->attempt($credentials)) {
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return back()->withErrors([
            'login' => 'Invalid email or password.',
        ])->withInput();
    }

    public function handleCroppedImage($base64data, $fName, $lName)
    {
        if ($base64data) {
            $microtime = str_replace('.', '', microtime(true));
            $this->objectData = $base64data;
            $this->objectName = 'customer/' . $fName . '_' . $lName . date('Ydm') . $microtime . '.png';
        } else {
            // if you are using this inside Livewire, this dispatch works.
            // otherwise you may want to replace with session flash.
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Image did not crop!!!')]);
            return;
        }
    }

    public function register(Request $request)
    {
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:customers,username',
            'email' => 'required|email|unique:customers,email',
            'business_module' => 'required|string|max:255',
            'phone_number' => 'required|string|max:15|unique:customer_profiles,phone_number',
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'password' => 'required|string|min:8|confirmed',
            'profile_picture_data' => 'nullable|string',
            'g-recaptcha-response' => ['required', new ReCaptcha],
            'terms_conditions' => 'accepted',
            'privacy_policy' => 'accepted',
        ]);

        // Conditional brand_name validation
        if ($request->business_module !== 'Personal') {
            $validatedData = array_merge(
                $validatedData,
                $request->validate([
                    'brand_name' => 'required|string|max:255|unique:customer_profiles,brand_name',
                ])
            );
        } else {
            $validatedData['brand_name'] = $request->input('brand_name', null);
        }

        try {
            DB::beginTransaction();

            // Firebase Authentication user creation
            $firebase = (new Factory)->withServiceAccount(base_path('resources/credentials/firebase_credentials.json'));
            $auth = $firebase->createAuth();

            $firebaseUser = $auth->createUser([
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'displayName' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
            ]);

            // Create customer
            $customer = Customer::create([
                'email' => $validatedData['email'],
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
                'status' => 1,
                'phone_verify' => 0,
                'uid' => $firebaseUser->uid,
            ]);

            if (!empty($validatedData['profile_picture_data'])) {
                $this->handleCroppedImage(
                    $validatedData['profile_picture_data'],
                    $validatedData['first_name'] ?? 'fuser',
                    $validatedData['last_name'] ?? 'luser'
                );
            }

            // Create profile
            $customer->customer_profile()->create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'business_module' => $validatedData['business_module'],
                'brand_name' => $validatedData['brand_name'] ?? null,
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'address' => $validatedData['address'],
                'zip_code' => $validatedData['zip_code'],
                'phone_number' => $validatedData['phone_number'],
                'avatar' => $this->objectName ?? null,
            ]);

            // Upload avatar if exists
            try {
                if ($this->objectName) {
                    $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->objectData));
                    Storage::disk('s3')->put($this->objectName, $croppedImage, 'public');
                }
            } catch (\Exception $e) {
                // Optional: replace with session flash if not Livewire
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
            }

            // Telegram notification (optional)
            try {
                Notification::route('toTelegram', null)->notify(new TeleNotifyCustomerNew(
                    $customer->id,
                    $customer->customer_profile->first_name . ' ' . $customer->customer_profile->last_name,
                    $customer->customer_profile->phone_number,
                    $customer->customer_profile->business_module,
                    $customer->customer_profile->brand_name,
                ));
            } catch (\Exception $e) {
                // ignore
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();

            // If Firebase user created, remove it
            try {
                if (isset($firebaseUser) && isset($auth)) {
                    $auth->deleteUser($firebaseUser->uid);
                }
            } catch (\Exception $ignored) {
            }

            session()->flash('alert', [
                'type' => 'error',
                'message' => __($e->getMessage()),
            ]);

            return back()->with('error', 'Error: ' . $e->getMessage());
        }

        // ✅ Phone OTP ONLY:
        // Send OTP immediately (optional but recommended)
        try {
            \App\Services\OtpService::sendPhoneOtp(
                customer: $customer,
                channel: config('otp.phone_channel'),
                lang: config('otp.default_lang', 'en')
            );
        } catch (\Exception $e) {
            // If OTP sending fails, you may still show OTP page and allow resend
        }

        return redirect()->route('goOTP', [
            'locale' => app()->getLocale(),
            'id' => $customer->id,
        ]);
    }

    /**
     * Phone OTP screen
     * ✅ Do NOT accept phone from URL (tamper-proof)
     */
    public function goOTP($locale, $id)
    {
        $customer = Customer::with('customer_profile')->findOrFail($id);

        // Optional: if already verified, skip OTP
        if ((int) $customer->phone_verify === 1) {
            Auth::guard('customer')->login($customer);
            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        return view('mains.components.otp.phone-otp', [
            'locale' => app()->getLocale(),
            'id' => $customer->id,
            'phone' => $customer->customer_profile->phone_number,
        ]);
    }

    public function goRePhoneOTP($locale, $uid)
    {
        return view('mains.components.otp.re-phone-otp', ['id' => $uid]);
    }

    public function updateRePhoneOTP($local, $uid, Request $request)
    {
        $customer = Customer::with('customer_profile')->find($uid);
        if (!$customer) {
            return abort(404);
        }

        $request->validate([
            'phone' => 'required|string|max:15',
        ]);

        $new_phone = $request->phone;

        $exists = Customer::whereHas('customer_profile', function ($query) use ($new_phone) {
            $query->where('phone_number', $new_phone);
        })->exists();

        if ($exists) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => __('Check Your Phone Numbe, Or Phone Has been Alreary Registerd'),
            ]);
        }

        $customer->customer_profile->phone_number = $new_phone;
        $customer->customer_profile->save();

        // Optional: reset verification if phone changes
        $customer->phone_verify = 0;
        $customer->save();

        // Optional: send OTP immediately after phone update
        try {
            \App\Services\OtpService::sendPhoneOtp(
                customer: $customer,
                channel: config('otp.phone_channel'),
                lang: config('otp.default_lang', 'en')
            );
        } catch (\Exception $e) {
        }

        return redirect()->route('goOTP', [
            'locale' => app()->getLocale(),
            'id' => $customer->id,
        ])->with('alert', [
            'type' => 'success',
            'message' => __('Phone Updated!'),
        ]);
    }

    /**
     * Resend Phone OTP
     * ✅ Do NOT accept phone from URL
     */
    public function resendPhoneOTP($locale, $id)
    {
        $customer = \App\Models\Customer::with('customer_profile')->findOrFail($id);

        $channel = request()->query('channel', config('otp.phone_channel'));
        $lang    = request()->query('lang', config('otp.default_lang', 'en'));

        try {
            $res = \App\Services\OtpService::sendPhoneOtp(
                customer: $customer,
                channel: $channel,
                lang:    $lang
            );

            if ($res['ok'] ?? false) {
                return response()->json(['success' => true]);
            }

            if (($res['reason'] ?? null) === 'cooldown') {
                return response()->json([
                    'success'   => false,
                    'cooldown'  => true,
                    'retry_in'  => $res['retry_in'] ?? 0,
                    'message'   => $res['message'] ?? 'Cooldown active',
                ], 429);
            }

            // IMPORTANT: send message back so JS can show it
            return response()->json([
                'success' => false,
                'message' => $res['message'] ?? 'OTP provider failed',
                'reason'  => $res['reason']  ?? null,
                'debug'   => app()->environment('local') ? $res : null,
            ], 422);

        } catch (\Throwable $e) {
            Log::error('Phone OTP resend failed', [
                'customer_id' => $customer->id,
                'channel'     => $channel,
                'error'       => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Server error while sending OTP',
                'error'   => app()->environment('local') ? $e->getMessage() : null,
            ], 500);
        }
    }


    /**
     * Verify Phone OTP
     */
    public function verifyOTP(Request $request)
    {
        $request->validate([
            'id' => 'required|integer|exists:customers,id',
            'entered_otp_code' => 'required|string',
        ]);

        $entered = $request->input('entered_otp_code');
        $customer = Customer::with('customer_profile')->findOrFail($request->input('id'));

        $res = \App\Services\OtpService::verifyPhoneOtp($customer, $entered);

        if ($res['ok'] ?? false) {
            // ✅ Mark verified
            $customer->phone_verify = 1;
            // if you have this column:
            // $customer->phone_verified_at = now();
            $customer->save();

            Auth::guard('customer')->login($customer);

            return redirect()->route('business.home', ['locale' => app()->getLocale()]);
        }

        $msg = match ($res['reason'] ?? '') {
            'expired' => __('Code expired, please resend.'),
            'no_code' => __('No code generated yet. Please resend.'),
            'mismatch' => __('Wrong Code!'),
            default => __('Verification failed.'),
        };

        return redirect()->back()->with('alert', ['type' => 'error', 'message' => $msg]);
    }

    public function logout()
    {
        Auth::guard('customer')->logout();
        return redirect()->route('business.home', ['locale' => app()->getLocale()]);
    }

    public function avatarupload(Request $request)
    {
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('profile_picture');
        $filename = time() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads'), $filename);

        return response()->json(['url' => asset('uploads/' . $filename)]);
    }

    /**
     * ✅ FIXED: updatePassword was using $customer->user()
     */
    public function updatePassword()
    {
        $customer = Auth::guard('customer')->user();
        if (!$customer) {
            return redirect()->back()->with('error', 'User not authenticated.');
        }

        $data = request()->validate([
            'old_password' => 'required',
            'new_password' => ['required', Password::min(8)->letters()->mixedCase()->numbers()->symbols()],
            'confirm_password' => 'required|same:new_password',
        ]);

        // Check old password
        if (!Hash::check($data['old_password'], $customer->password)) {
            return back()->withErrors(['old_password' => 'Old password is incorrect.']);
        }

        // Prevent same password
        if ($data['old_password'] === $data['new_password']) {
            return back()->withErrors(['new_password' => 'New password cannot be the same as the old password.']);
        }

        $customer->update([
            'password' => Hash::make($data['new_password']),
        ]);

        return redirect()->back()->with('status', __('Password updated successfully.'));
    }

    // Reset Password flow
    public function showLinkRequestForm($locale)
    {
        return view('mains.components.account.reset-password');
    }

    public function sendResetLinkEmail($locale, Request $request)
    {
        $request->validate(['email' => 'required|email|exists:customers,email']);

        $status = Password::broker('customers')->sendResetLink(
            $request->only('email'),
            function ($customer, $token) use ($locale) {
                $customer->notify(new ResetPasswordNotification($locale, $token));
            }
        );

        return $status === Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withErrors(['email' => __($status)]);
    }

    public function showResetForm($locale, $token)
    {
        return view('mains.components.account.update-password', ['token' => $token]);
    }

    public function reset(Request $request)
    {
        $request->validate([
            'email' => 'required|email|exists:customers,email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        $response = Password::broker('customers')->reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($customer, $password) {
                $customer->password = Hash::make($password);
                $customer->save();
            }
        );

        return $response == Password::PASSWORD_RESET
            ? redirect()->route('password.successResetMsg', ['locale' => app()->getlocale()])->with('status', trans($response))
            : back()->withErrors(['email' => trans($response)]);
    }

    public function successResetMsg()
    {
        return view('mains.components.account.password-check-success');
    }
}
