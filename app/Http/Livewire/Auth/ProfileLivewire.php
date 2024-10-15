<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use App\Models\Profile;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class ProfileLivewire extends Component
{
    use WithPagination;
    protected $fAuth;
    // INT
    public $filteredLocales;
    public $userImg;
    public $glang;
    // EDIT
    public $fNameEdit;
    public $lNameEdit;
    public $usernameEdit;
    // public $positionEdit;
    public $phoneEdit;
    public $emailEdit;
    // public $statusEdit;
    // public $rolesEdit = [];
    public $user_update;
    // Temp
    public $de = 1;
    public $objectReader;
    public $objectName;
    public $objectData;
    // PASSWORD UPDATE
    public $old_password;
    public $new_password;
    public $new_password_confirmation;
    // VALIDATION
    public $currentValidation = 'update';
    //LISTENERS
    protected $listeners = [
        'imgCrop' => 'handleCroppedImage',
        'filterUsers' => 'filterUsers',
    ];

    // On Load
    public function mount(){
        $this->userImg = app('userImg');
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
    }

    // VALIDATION
    protected function rules()
    {
        //Keep It Empty
    }

    protected function rulesForPassword()
    {
        return [
            'old_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ];
    }
    protected function rulesForUpdate()
    {
        $userId = $this->user_update->id;
        return [
            'fNameEdit' => ['required', 'string', 'max:255'],
            'lNameEdit' => ['required', 'string', 'max:255'],
            'usernameEdit' => ['required', 'string', 'max:255'],
            'phoneEdit' => ['required', 'string', 'max:255'],
            // 'positionEdit' => ['required', 'string'],
            'emailEdit' => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($userId) // Ignore current user's ID for uniqueness check
            ],
            // 'rolesEdit' => ['required', 'array'],  // Ensure roles is an array (multiple roles)
            // 'statusEdit' => ['required', 'in:0,1'],  // Status validation
        ];
    }

    // Real-Time Validation
    public function updated($propertyName)
    {
        // $this->validateOnly($propertyName);
        // $this->validateOnly($propertyName, $this->rulesForUpdate());
        if($this->currentValidation == 'update') {
            $this->validateOnly($propertyName, $this->rulesForUpdate());
        } else {
            $this->validateOnly($propertyName, $this->rulesForPassword());
        }
    }
    
    // SETTER
    public function setFirebaseAuth(FirebaseAuth $auth) {
        $this->fAuth = $auth;
    }

    public function updateValidation() {
        $this->currentValidation = 'psaaword';

    }
    public function editUser(int $id) {
        $this->currentValidation = 'update';
        // dd('you clicked me');
        $this->de = 0;
        // $a = Brand::where('id',$id)->first();
        $user_edit = User::find($id);
        $this->user_update = $user_edit;

        if ($user_edit) {
            $this->fNameEdit = $user_edit->profile->first_name;
            $this->lNameEdit = $user_edit->profile->last_name;
            $this->usernameEdit = $user_edit->username;
            $this->phoneEdit = $user_edit->profile->phone_number;
            // $this->positionEdit = $user_edit->profile->position;
            $this->emailEdit = $user_edit->email;
            // $this->statusEdit = $user_edit->status;
            $this->objectReader = $user_edit->profile->avatar;

            // $this->rolesEdit = $user_edit->roles->pluck('id')->toArray();
        } else {
        // error message
        }

    }
    public function updateUser (FirebaseAuth $auth) {
        $validatedData = $this->validate($this->rulesForUpdate());
        $this->setFirebaseAuth($auth);
        try {
            if($this->objectData) {
                $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->objectData));

                if($this->objectReader){
                        Storage::disk('s3')->delete($this->objectReader);
                        Storage::disk('s3')->put($this->objectName, $croppedImage , 'public');
                    } else {
                        Storage::disk('s3')->put($this->objectName, $croppedImage , 'public');               
                }
            } else {
                $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Image Did Not Update')]);
                // $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, Please Upload New Image')]);
            }
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
            return;
        }

        User::where('id', $this->user_update->id)->update([
            'username' => $validatedData['usernameEdit'],
            'email' => $validatedData['emailEdit'],
            // 'status' => $validatedData['statusEdit'],
        ]);
        
        
        Profile::where('user_id', $this->user_update->id)->update([
            'first_name' => $validatedData['fNameEdit'],
            'last_name' => $validatedData['lNameEdit'],
            // 'position' => $validatedData['positionEdit'],
            'phone_number' => $validatedData['phoneEdit'],
            'avatar' => $this->objectName ?? $this->objectReader,
        ]);

        // $this->user_update->roles()->sync($validatedData['rolesEdit']);

        if ($this->user_update->email !== $validatedData['emailEdit']) {
            // Update email in Firebase only if it's changed
            $this->fAuth->updateUser($this->user_update->uid, [
                'email' => $validatedData['emailEdit']
            ]);
        }
    
        $this->closeModal();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Brand Updated Successfully')]);
    }

    public function sadImage () { 
        $this->de = 1;
    }

    // CRUD Handler
    public function handleCroppedImage($base64data)
    {
        // dd($base64data);
        if ($base64data){
            $microtime = str_replace('.', '', microtime(true));
            $this->objectData = $base64data;
            $this->objectName = 'users/' . ($this->fNameEdit . '_' . $this->lNameEdit ?? 'user') . '_user_'.date('Ydm') . $microtime . '.png';
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return;
            // return 'failed to crop image code...405';
        }
    }

    // RESET BUTTON
    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
        $this->de = 1;
    }
 
    public function resetInput()
    {
        $this->fNameEdit = null;
        $this->lNameEdit = null;
        $this->usernameEdit = null;
        // $this->positionEdit = null;
        $this->phoneEdit = null;
        $this->emailEdit = null;
        // $this->rolesEdit = [];
        $this->user_update = null;
        // $this->status = 1;
        // $this->priority = Brand::max('priority') + 1;
        // $this->statusEdit = 1;
        // $this->priorityEdit = '';
        $this->objectReader = null;
        $this->objectName = null;
        $this->objectData = null;

        $this->emit('resetData');
        $this->emit('resetEditData');
    }

    public function updatePassword()
    {
        try {
            $this->validate($this->rulesForPassword());

            $user = Auth::user();
    
            if (!Hash::check($this->old_password, $user->password)) {
                $this->addError('old_password', 'Current password is incorrect.');
                return;
            }
    
            $user->password = Hash::make($this->new_password);
            $user->save();
    
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Password Updated Successfully ')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something Went Wrong')]);

        }

    }

    // Render
    public function render(){        
        $query = User::with(['profile', 'roles'])->where('id', auth()->guard('admin')->user()->id)->first();

        return view('super-admins.pages.profiles.profile', [
            'query' => $query,
        ]);
    }
}