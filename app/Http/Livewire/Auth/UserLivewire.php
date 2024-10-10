<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use App\Models\Profile;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use App\Models\BrandTranslation;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class UserLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['statusFilter', 'page'];
    protected $fAuth;
    // INT
    public $filteredLocales;
    public $userImg;
    // Create
    public $fName;
    public $lName;
    public $username;
    public $position;
    public $email;
    public $phone;
    public $password;
    public $status;
    public $glang;
    public $roles = [];
    // EDIT
    public $fNameEdit;
    public $lNameEdit;
    public $usernameEdit;
    public $positionEdit;
    public $phoneEdit;
    public $emailEdit;
    public $statusEdit;
    public $rolesEdit = [];
    public $user_update;
    // DELETE
    public $brand_selected_id_delete;
    public $brand_name_selected_delete;
    public $showTextTemp;
    public $confirmDelete;
    // Render
    public $search = '';
    public $statusFilter = 'all';
    public $page = 1;
    public $activeCount = 0;
    public $nonActiveCount = 0;
    // Temp
    public $de = 1;
    public $objectReader;
    public $objectName;
    public $objectData;
    // VALIDATION
    public $currentValidation = 'add';
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
        // Default Values
        $this->status = 1;
        $this->statusFilter = request()->query('statusFilter', 'all');
        $this->page = request()->query('page', 1);
    }

    // VALIDATION
    protected function rules()
    {
        //Keep It Empty
    }

    protected function rulesForSave()
    {
        return [
            'fName' => ['required', 'string', 'max:255'],
            'lName' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:255'],
            'position' => ['required', 'string'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],  // Ensure email is unique
            'password' => ['required', 'string', 'min:8'],  // Add password validation
            'roles' => ['required', 'array'],  // Ensure roles is an array (multiple roles)
            'status' => ['required', 'in:0,1'],  // Status validation
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
            'positionEdit' => ['required', 'string'],
            'emailEdit' => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($userId) // Ignore current user's ID for uniqueness check
            ],
            'rolesEdit' => ['required', 'array'],  // Ensure roles is an array (multiple roles)
            'statusEdit' => ['required', 'in:0,1'],  // Status validation
        ];
    }

    // Real-Time Validation
    public function updated($propertyName)
    {
        // $this->validateOnly($propertyName);
        if($this->currentValidation == 'add') {
            $this->validateOnly($propertyName, $this->rulesForSave());
        } else {
            $this->validateOnly($propertyName, $this->rulesForUpdate());
        }
    }
    
    // SETTER
    public function setFirebaseAuth(FirebaseAuth $auth) {
        $this->fAuth = $auth;
    }
    //CRUD
    public function saveUser(FirebaseAuth $auth) {
        $this->currentValidation = 'add';
        $validatedData = $this->validate($this->rulesForSave());
    
        // Set FirebaseAuth object using setter method
        $this->setFirebaseAuth($auth);
    
        try {
            // Upload the image
            if ($this->objectName) {
                $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->objectData));
                Storage::disk('s3')->put($this->objectName, $croppedImage, 'public');
            } else {
                $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, Please Upload The Image')]);
                return;
            }
    
            // Create a new user in Firebase
            try {
                $userProperties = [
                    'email' => $validatedData['email'],
                    'password' => $validatedData['password'],
                    'displayName' => $validatedData['fName'] . ' ' . $validatedData['lName'],
                ];
                $firebaseUser = $this->fAuth->createUser($userProperties);
    
                // Create the user in your database
                $user = User::create([
                    'username' => $validatedData['username'],
                    'email' => $validatedData['email'],
                    'password' => bcrypt($validatedData['password']),  // Ensure password is hashed
                    'status' => $validatedData['status'],
                    'uid' => $firebaseUser->uid,
                ]);
                
                Profile::create([
                    'user_id' => $user->id,
                    'position' => $validatedData['position'],
                    'first_name' => $validatedData['fName'],
                    'last_name' => $validatedData['lName'],
                    'phone_number' => $validatedData['phone'],
                    'avatar' => $this->objectName,
                ]);
    
            } catch (\Exception $e) {
                if (isset($firebaseUser)) {
                    $this->fAuth->deleteUser($firebaseUser->uid);
                }
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('F-Error: ' . $e->getMessage())]);
            }
            // Assign roles
            $user->roles()->sync($validatedData['roles']);
            // $this->filterUsers($this->statusFilter);
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('New User Added Successfully')]);
    
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Error: ' . $e->getMessage())]);
        }
    }


    public function editUser(int $id) {
        $this->currentValidation = 'edit';
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
            $this->positionEdit = $user_edit->profile->position;
            $this->emailEdit = $user_edit->email;
            $this->statusEdit = $user_edit->status;
            $this->objectReader = $user_edit->profile->avatar;

            $this->rolesEdit = $user_edit->roles->pluck('id')->toArray();
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
            'status' => $validatedData['statusEdit'],
        ]);
        
        
        Profile::where('user_id', $this->user_update->id)->update([
            'first_name' => $validatedData['fNameEdit'],
            'last_name' => $validatedData['lNameEdit'],
            'position' => $validatedData['positionEdit'],
            'phone_number' => $validatedData['phoneEdit'],
            'avatar' => $this->objectName ?? $this->objectReader,
        ]);

        $this->user_update->roles()->sync($validatedData['rolesEdit']);

        if ($this->user_update->email !== $validatedData['emailEdit']) {
            // Update email in Firebase only if it's changed
            $this->fAuth->updateUser($this->user_update->uid, [
                'email' => $validatedData['emailEdit']
            ]);
        }
    
        $this->closeModal();
        $this->filterUsers($this->statusFilter);
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('User Updated Successfully')]);
    }


    // public function removeUser (int $id) {
    //     $this->brand_selected_id_delete = Brand::find($id);
    //     $this->brand_name_selected_delete = BrandTranslation::where('brand_id', $id)->where('locale', app()->getLocale())->first() ?? "Delete";
    //     if ($this->brand_name_selected_delete) {
    //         $this->showTextTemp = $this->brand_name_selected_delete->name;
    //         $this->confirmDelete = true;
    //     } else {
    //         $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
    //     }
    // }

    public $brandNameToDelete = '';
    // public function destroyBrand () {
    //     if ($this->confirmDelete && $this->brandNameToDelete === $this->showTextTemp) {
    //         try {
    //             if($this->brand_selected_id_delete->image) {
    //                 Storage::disk('s3')->delete($this->brand_selected_id_delete->image);
    //             } else {
    //                 $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('Something Went Wrong, Image Did Not Removed From Server')]);
    //             }
    //         } catch (\Exception $e) {
    //             $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
    //             return;
    //         }
    //         Brand::find($this->brand_selected_id_delete->id)->delete();
    //         $this->closeModal();
    //         $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Brand Deleted Successfully')]);

    //         $this->confirmDelete = false;
    //         $this->brand_selected_id_delete = null;
    //         $this->brand_name_selected_delete = null;
    //         $this->brandNameToDelete = '';
    //         $this->showTextTemp = null;
    //     } else {
    //         $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Operaiton Faild')]);
    //     }
    // }

    public function sadImage () { 
        $this->de = 1;
    }


    public function updateStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $brandStatus = User::find($id);
    
        if ($brandStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $brandStatus->status = !$brandStatus->status;
            $brandStatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }

    // CRUD Handler
    public function handleCroppedImage($base64data)
    {
        // dd($base64data);
        if ($base64data){
            $microtime = str_replace('.', '', microtime(true));
            $this->objectData = $base64data;
            $this->objectName = 'users/' . ($this->fName . '_' . $this->lName ?? $this->fNameEdit . '_' . $this->lNameEdit ?? 'user') . '_user_'.date('Ydm') . $microtime . '.png';
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return;
            // return 'failed to crop image code...405';
        }
    }

    public function filterUsers($status)
    {
        $this->statusFilter = $status;
    }

    // QUERY FUNCTION
    public function changeTab($status)
    {
        $this->statusFilter = $status;
        $this->page = 1;
        $this->emitSelf('refresh');
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

        $this->fName = null;
        $this->lName = null;
        $this->username = null;
        $this->position = null;
        $this->email = null;
        $this->phone = null;
        $this->password = null;
        $this->status = null;
        $this->roles = [];
        $this->fNameEdit = null;
        $this->lNameEdit = null;
        $this->usernameEdit = null;
        $this->positionEdit = null;
        $this->phoneEdit = null;
        $this->emailEdit = null;
        $this->rolesEdit = [];
        $this->user_update = null;
        $this->status = 1;
        // $this->priority = Brand::max('priority') + 1;
        $this->statusEdit = 1;
        // $this->priorityEdit = '';
        $this->objectReader = null;
        $this->objectName = null;
        $this->objectData = null;

        $this->emit('resetData');
        $this->emit('resetEditData');
    }


    // Render
    public function render(){
        $this->activeCount = User::where('status', 1)->count() ?? 0;
        $this->nonActiveCount = User::where('status', 0)->count() ?? 0;

        $query = User::with(['profile', 'roles']);
        
        // Apply status filter
        if ($this->statusFilter === 'active') {
            $query->where('status', 1);
        } elseif ($this->statusFilter === 'non-active') {
            $query->where('status', 0);
        }
    
        // Apply search filter based on translations
        if (!empty($this->search)) {
            $query->where(function ($query) {
                $query->whereHas('profile', function ($query) {
                    $query->where('first_name', 'like', '%' . $this->search . '%')
                          ->orWhere('last_name', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('roles', function ($query) {
                    $query->where('name', 'like', '%' . $this->search . '%');
                });
            });
        }
        
    
        $tableData = $query->orderBy('created_at', 'DESC')->paginate(10)->withQueryString();

        return view('super-admins.pages.users.users-table', [
            'tableData' => $tableData,
            'objectReader' => $this->objectReader
        ]);
    }
}