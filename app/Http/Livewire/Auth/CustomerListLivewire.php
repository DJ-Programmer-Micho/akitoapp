<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use App\Models\Brand;
use App\Models\Profile;
use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Str;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CustomerProfile;
use Illuminate\Validation\Rule;
use App\Models\BrandTranslation;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class CustomerListLivewire extends Component
{
    use WithPagination;
    protected $queryString = ['statusFilter', 'page'];
    protected $fAuth;
    // INT
    public $filteredLocales;
    public $customerImg;
    public $glang;
    // EDIT
    public $fNameEdit;
    public $lNameEdit;
    public $phoneEdit;
    public $emailEdit;
    public $statusEdit;
    public $countryEdit; 
    public $cityEdit; 
    public $addressEdit;
    public $zipcodeEdit;
    public $customer_update;

    // DELETE
    public $brand_selected_id_delete;
    public $brand_name_selected_delete;
    public $showTextTemp;
    public $confirmDelete;
    // Render
    public $searchTerm = '';
    public $startDate = '';
    public $statusFilter = 'all';
    public $page = 1;
    // Temp
    public $de = 1;
    public $objectReader;
    public $objectName;
    public $objectData;
    // VALIDATION
    public $currentValidation = 'edit';
    //LISTENERS
    protected $listeners = [
        'imgCrop' => 'handleCroppedImage',
        'filterUsers' => 'filterUsers',
    ];

    // On Load
    public function mount(){
        $this->customerImg = app('userImg');
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        // Default Values
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
        $userId = $this->customer_update->id;
        
        return [
            'emailEdit' => [
                'required', 
                'email', 
                'max:255', 
                Rule::unique('users', 'email')->ignore($userId) // Ignore current user's ID for uniqueness check
            ],
            'fNameEdit' => ['required', 'string', 'max:255'],
            'lNameEdit' => ['required', 'string', 'max:255'],
            'phoneEdit' => ['required', 'string', 'max:255'],
            'countryEdit' => ['required', 'string', 'max:255'],
            'cityEdit' => ['required', 'string', 'max:255'],
            'addressEdit' => ['required', 'string', 'max:255'],
            'zipcodeEdit' => ['required', 'string', 'max:255'],
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
    // public function saveUser(FirebaseAuth $auth) {
    //     $this->currentValidation = 'add';
    //     $validatedData = $this->validate($this->rulesForSave());
    
    //     // Set FirebaseAuth object using setter method
    //     $this->setFirebaseAuth($auth);
    
    //     try {
    //         // Upload the image
    //         if ($this->objectName) {
    //             $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $this->objectData));
    //             Storage::disk('s3')->put($this->objectName, $croppedImage, 'public');
    //         } else {
    //             $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong, Please Upload The Image')]);
    //             return;
    //         }
    
    //         // Create a new user in Firebase
    //         try {
    //             $userProperties = [
    //                 'email' => $validatedData['email'],
    //                 'password' => $validatedData['password'],
    //                 'displayName' => $validatedData['fName'] . ' ' . $validatedData['lName'],
    //             ];
    //             $firebaseUser = $this->fAuth->createUser($userProperties);
    
    //             // Create the user in your database
    //             $user = User::create([
    //                 'username' => $validatedData['username'],
    //                 'email' => $validatedData['email'],
    //                 'password' => bcrypt($validatedData['password']),  // Ensure password is hashed
    //                 'status' => $validatedData['status'],
    //                 'uid' => $firebaseUser->uid,
    //             ]);
                
    //             Profile::create([
    //                 'user_id' => $user->id,
    //                 'position' => $validatedData['position'],
    //                 'first_name' => $validatedData['fName'],
    //                 'last_name' => $validatedData['lName'],
    //                 'phone_number' => $validatedData['phone'],
    //                 'avatar' => $this->objectName,
    //             ]);
    
    //         } catch (\Exception $e) {
    //             if (isset($firebaseUser)) {
    //                 $this->fAuth->deleteUser($firebaseUser->uid);
    //             }
    //             $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('F-Error: ' . $e->getMessage())]);
    //         }
    //         // Assign roles
    //         $user->roles()->sync($validatedData['roles']);
    //         // $this->filterUsers($this->statusFilter);
    //         $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('New User Added Successfully')]);
    
    //     } catch (\Exception $e) {
    //         $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Error: ' . $e->getMessage())]);
    //     }
    // }


    public function editUser(int $id) {
        $this->currentValidation = 'edit';
        $customer_edit = Customer::find($id);
        $this->customer_update = $customer_edit;

        if ($customer_edit) {
            $this->emailEdit = $customer_edit->email;
            $this->statusEdit = $customer_edit->status;
            $this->fNameEdit = $customer_edit->customer_profile->first_name;
            $this->lNameEdit = $customer_edit->customer_profile->last_name;
            $this->countryEdit = $customer_edit->customer_profile->country;
            $this->cityEdit = $customer_edit->customer_profile->city;
            $this->addressEdit = $customer_edit->customer_profile->address;
            $this->zipcodeEdit = $customer_edit->customer_profile->zip_code;
            $this->phoneEdit = $customer_edit->customer_profile->phone_number;
            $this->objectReader = $customer_edit->customer_profile->avatar;
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

        Customer::where('id', $this->customer_update->id)->update([
            'email' => $validatedData['emailEdit'],
            'status' => $validatedData['statusEdit'],
        ]);
        
        CustomerProfile::where('customer_id', $this->customer_update->id)->update([
            'first_name' => $validatedData['fNameEdit'],
            'last_name' => $validatedData['lNameEdit'],
            'country' => $validatedData['countryEdit'],
            'city' => $validatedData['cityEdit'],
            'address' => $validatedData['addressEdit'],
            'zip_code' => $validatedData['zipcodeEdit'],
            'phone_number' => $validatedData['phoneEdit'],
            'avatar' => $this->objectName ?? $this->objectReader,
        ]);

        if ($this->customer_update->email !== $validatedData['emailEdit']) {
            // Update email in Firebase only if it's changed
            $this->fAuth->updateUser($this->customer_update->uid, [
                'email' => $validatedData['emailEdit']
            ]);
        }
    
        $this->closeModal();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Customer Updated Successfully')]);
    }

    public function sadImage () { 
        $this->de = 1;
    }

    public function updateStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $brandStatus = Customer::find($id);
    
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
        $this->phoneEdit = null;
        $this->emailEdit = null;
        $this->customer_update = null;
        $this->statusEdit = 1;
        $this->objectReader = null;
        $this->objectName = null;
        $this->objectData = null;

        $this->emit('resetData');
        $this->emit('resetEditData');
    }


    // Render
    public function render(){
        $query = Customer::with(['customer_profile']);
        
        // Apply status filter
        if ($this->statusFilter == '1') {
            $query->where('status', 1);  // Active
        } elseif ($this->statusFilter == '0') {
            $query->where('status', 0);  // Block
        }        
    
        // Apply search filter based on translations
        if (!empty($this->searchTerm)) {
            $query->where(function ($query) {
                $query->whereHas('customer_profile', function ($q) {
                    $q->where('first_name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('last_name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('phone_number', 'like', '%' . $this->searchTerm . '%')  // Correct phone search
                      ->orWhere('country', 'like', '%' . $this->searchTerm . '%')  // Correct phone search
                      ->orWhere('city', 'like', '%' . $this->searchTerm . '%')  // Correct phone search
                      ->orWhere('address', 'like', '%' . $this->searchTerm . '%');  // Correct phone search
                })
                ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
            });
        }
        if (!empty($this->startDate)) {
            $query->whereDate('created_at', '>=', $this->startDate);            
        } 
        
        if  (!empty($this->endDate)) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }
        
    
        $tableData = $query->orderBy('created_at', 'DESC')->paginate(10)->withQueryString();
        return view('super-admins.pages.customerlist.customer-list', [
            'tableData' => $tableData,
            'objectReader' => $this->objectReader
        ]);
    }
}