<?php

namespace App\Http\Controllers\Customer;

use App\Models\Customer;
use App\Rules\ReCaptcha;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
// use App\Services\FirebaseService;
use App\Mail\EmailVerificationMail;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;



class CustomerAuth extends Controller
{
    public function login(Request $request)
    {
        // Validate the form data
        $request->validate([
            'login' => 'required|string',
            'password-form-signin' => 'required|string|min:8',
            'g-recaptcha-response' => ['required', new ReCaptcha]
        ]);
    
        // Map form field names to the correct database column names
        $loginType = filter_var($request->input('login'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        $credentials = [
            $loginType => $request->input('login'),
            'password' => $request->input('password-form-signin'),
        ];
    
        if (Auth::guard('customer')->attempt($credentials)) {
            // Authentication successful
            return redirect()->route('business.home', ['locale' => 'en']); // Adjust the route as needed
        }
    
        // If authentication fails
        return back()->withErrors([
            'email-form-signin' => 'Invalid email or password.',
        ])->withInput();
    }
    



    public $objectData;
    public $objectName;
    public function handleCroppedImage($base64data, $fName, $lName)
    {
        if ($base64data){
            $microtime = str_replace('.', '', microtime(true));
            $this->objectData = $base64data;
            $this->objectName = 'customer/' . $fName .'_'. $lName .date('Ydm') . $microtime . '.png';
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return;
            // return 'failed to crop image code...405';
        }
    }

    public function register(Request $request)
    {

        // Validate the request data
        $validatedData = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:customers,username',
            'email' => 'required|email|unique:customers,email',
            'phone_number' => 'required|string|max:15|unique:customer_profiles,phone_number', // Checking uniqueness in 'customer_profiles' table
            'country' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'zip_code' => 'required|string|max:10',
            'password' => 'required|string|min:8|confirmed', // 'password' and 'password_confirmation' should match
            'profile_picture_data' => 'nullable|string', // This will be your base64 encoded image
            'g-recaptcha-response' => ['required', new ReCaptcha]
        ]);




        try {
            // Create new customer in the database
            $customer = Customer::create([
                'email' => $validatedData['email'],
                'username' => $validatedData['username'],
                'password' => Hash::make($validatedData['password']),
                'status' => 1, // Default status
                'phone_verify' => 1, // OTP non-functional for now
                // 'phone_otp_number' and 'phone_verified_at' are left for future implementation
            ]);

            if($validatedData['profile_picture_data']){
                $this->handleCroppedImage($validatedData['profile_picture_data'], $validatedData['first_name'] ?? 'fuser', $validatedData['last_name'] ?? 'luser');
            }

            

            // Create customer profile
            $customer->customer_profile()->create([
                'first_name' => $validatedData['first_name'],
                'last_name' => $validatedData['last_name'],
                'country' => $validatedData['country'],
                'city' => $validatedData['city'],
                'address' => $validatedData['address'],
                'zip_code' => $validatedData['zip_code'],
                'phone_number' => $validatedData['phone_number'], // Store phone number in profile too
                'avatar' => $this->objectName ?? null, // Store profile picture path in AWS S3 if uploaded
            ]);
            try {
                if($this->objectName) {
                    $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->objectData));
                    Storage::disk('s3')->put($this->objectName, $croppedImage , 'public');
                } else {
                    $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, Please Uplaod The Image')]);
                    return;
                }
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
            }

            // Store customer in Firebase Authentication
            $firebase = (new Factory)->withServiceAccount(base_path('resources/credentials/firebase_credentials.json')); // Path to your Firebase credentials
            $auth = $firebase->createAuth();
            $firebaseUser = $auth->createUser([
                'email' => $validatedData['email'],
                'password' => $validatedData['password'],
                'displayName' => $validatedData['first_name'] . ' ' . $validatedData['last_name'],
            ]);

        } catch (\Exception $e) {
            // If an error occurs during Firebase user creation, rollback
            if (isset($firebaseUser)) {
                $auth->deleteUser($firebaseUser->uid); // Delete Firebase user if created
            }

            return back()->with('error', 'Error: ' . $e->getMessage());
        }
        
        // Redirect or return success message
        return redirect()->route('goEmailOTP', ['locale' => app()->getLocale(), 'id' => $customer->id, 'email' => $customer->email]);
        // return redirect()->route('business.account', ['locale' => app()->getLocale()]);
    }

    public function goEmailOTP($local, $uid, $email){
        return view('mains.components.otp.email-otp',['id' => $uid, 'email' => $email]);
    } // END Function (Register)

    public function resendEmailOTP($local, $id, $email){
        $customer = Customer::where('email', $email)->first();
        if ($customer) {
            $otpCodeEmail = rand(100000, 999999);

            $customer->updateOrCreate(
                ['id' => $customer->id], // Use the primary key column for identification
                ['email_otp_number' => $otpCodeEmail]
            );
            // Send OTP via email (Mailtrap)
            Mail::to($customer->email)->send(new EmailVerificationMail($otpCodeEmail));
            session()->flash('alert', [
                'type' => 'success',
                'message' => __('PIN CODE SENT!, Please Check Your Email'),
            ]);
            return redirect()->route('goEmailOTP', ['locale' => app()->getLocale(), 'id' => $id, 'email' => $customer->email]);
        } else {
            return redirect()->back()->with('error', 'User not found.');
        }
    }
    
    public function verifyEmailOTP(Request $request)
    {
        // Verify email OTP code...
        $enteredEmailOTP = $request->input('entered_email_otp_code');
        $customer = Customer::where('email', $request->input('email'))->first();

        if ($customer && $enteredEmailOTP == $customer->email_otp_number) {
            $customer->email_verify = 1;
            $customer->save();

            Auth::guard('customer')->login($customer);

            return redirect()->route('business.home', ['locale' => 'en']);
        }
        session()->flash('alert', [
            'type' => 'error',
            'message' => __('PIN CODE NOT CORRECT!'),
        ]);
    }

    public function goReEmailOTP($local, $uid){
        return view('mains.components.otp.re-email-otp',['locale' => app()->getLocale(), 'id' => $uid]);
    } // END Function (Register)

    public function updateReEmailOTP($local, $uid, Request $request){
    // dd($request->all());
    $customer = Customer::find($uid);
    if (!$customer) {
        return abort(404); // Or handle the case where the user is not found
    }
        $new_email = $request->email;
        if(Customer::where('email', $new_email)->exists()) {
            return redirect()->back()->with('alert', [
                'type' => 'error',
                'message' => __('Check Your Email Spelling, Or Email Has been Alreary Registerd'),
            ]);
        } else {
            $customer->email = $new_email;
            $customer->save();

            // try {
            //     $firebaseAuth = $firebaseService->getAuth();
                
            //     // Assuming you store Firebase UID in the customer record
            //     $firebaseAuth->updateUser($customer->firebase_uid, [
            //         'email' => $new_email
            //     ]);
        
            //     // If successful, update the local database
            //     $customer->email = $new_email;
            //     $customer->save();
        
            //     return redirect()->route('goEmailOTP', ['locale' => app()->getLocale(), 'id' => $customer->id, 'email' => $new_email])->with('alert', [
            //         'type' => 'success',
            //         'message' => __('Email Updated!'),
            //     ]);
            // } catch (\Kreait\Firebase\Exception\AuthException $e) {
            //     // Handle Firebase errors
            //     return redirect()->back()->with('alert', [
            //         'type' => 'error',
            //         'message' => __('Failed to update email in Firebase: ') . $e->getMessage(),
            //     ]);
            // }
            
            return redirect()->route('goEmailOTP', ['locale' => app()->getLocale(), 'id' => $customer->id, 'email' => $new_email])->with('alert', [
                'type' => 'success',
                'message' => __('Email Updated!'),
            ]);
        }
    } // END Function (update email)


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
