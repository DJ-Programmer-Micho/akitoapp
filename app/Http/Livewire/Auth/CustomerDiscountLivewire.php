<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Profile;
use Livewire\Component;
use App\Models\Category;
use App\Models\Customer;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use App\Models\DiscountRule;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\CustomerProfile;
use Illuminate\Validation\Rule;
use App\Models\BrandTranslation;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class CustomerDiscountLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $queryString = ['statusFilter', 'page'];
    // INT
    public $filteredLocales;
    public $customerImg;
    public $glang;
    public $statusFilter;
    // EDIT
    // data
    public $customers;
    public $selectedCustomer; //customer_id
    public $fetchedData = [];
    public $selectedType; //type_id
    public $selectedData; //data_id
    public $percentageDiscount;
    // VALIDATION
    public $discountRuleId;
    public $currentValidation = 'add';
    // FILTERS
    public $searchTerm = '';
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
        // Fetch Init Data
        $this->fetchCustomers();
    }
    
    private function fetchCustomers() {
        $this->customers = Customer::with('customer_profile:id,customer_id,first_name,last_name')
            ->get(['id']);
    }
    public function fetchData()
    {
        if ($this->selectedType == 'brand') {
            $this->fetchedData = Brand::with(['brandtranslation' => function ($query) {
                $query->where('locale', $this->glang);
            }])->get();
        } elseif ($this->selectedType == 'category') {
            $this->fetchedData = Category::with(['categoryTranslation' => function ($query) {
                $query->where('locale', $this->glang);
            }])->get();
        } elseif ($this->selectedType == 'subcategory') {
            $this->fetchedData = SubCategory::with(['subCategoryTranslation' => function ($query) {
                $query->where('locale', $this->glang);
            }])->get();
        } elseif ($this->selectedType == 'product') {
            $this->fetchedData = Product::with(['productTranslation' => function ($query) {
                $query->where('locale', $this->glang);
            }])->get();
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Something Went Wrong')]);
        }
    }

    // VALIDATION
    protected function rules()
    {
        //Keep It Empty
    }

    public function switchRole(){
        $this->currentValidation = 'add';
    }
    

    protected function rulesForSave()
    {
        $rules = [
            'selectedCustomer' => 'required|exists:customers,id',
            'selectedType' => 'required|in:brand,category,subcategory,product',
            'percentageDiscount' => 'required|numeric|min:0|max:100',
        ];
    
        // Add additional rules based on the selected type
        if ($this->selectedType == 'brand') {
            $rules['selectedData'] = 'nullable|exists:brands,id';
        } elseif ($this->selectedType == 'category') {
            $rules['selectedData'] = 'nullable|exists:categories,id';
        } elseif ($this->selectedType == 'subcategory') {
            $rules['selectedData'] = 'nullable|exists:sub_categories,id';
        } elseif ($this->selectedType == 'product') {
            $rules['selectedData'] = 'nullable|exists:products,id';
        }
    
        return $rules;
    }

    protected function rulesForUpdate()
    {
        $rules = [
            'selectedCustomer' => 'required|exists:customers,id',
            'selectedType' => 'required|in:brand,category,subcategory,product',
            'percentageDiscount' => 'required|numeric|min:0|max:100',
        ];
    
        // Add additional rules based on the selected type
        if ($this->selectedType == 'brand') {
            $rules['selectedData'] = 'nullable|exists:brands,id';
        } elseif ($this->selectedType == 'category') {
            $rules['selectedData'] = 'nullable|exists:categories,id';
        } elseif ($this->selectedType == 'subcategory') {
            $rules['selectedData'] = 'nullable|exists:sub_categories,id';
        } elseif ($this->selectedType == 'product') {
            $rules['selectedData'] = 'nullable|exists:products,id';
        }
    
        return $rules;
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
    public function saveDiscount()
    {
        $this->currentValidation = 'add';
    
        // Validate the data
        $validatedData = $this->validate($this->rulesForSave());
        try {
            // Prepare the base data
            $data = [
                'customer_id' => $validatedData['selectedCustomer'],
                'type' => $validatedData['selectedType'],
                'discount_percentage' => $validatedData['percentageDiscount'],
            ];
    
            // Conditionally add the correct type ID
            if ($validatedData['selectedType'] == 'brand') {
                $data['brand_id'] = $validatedData['selectedData'];
            } elseif ($validatedData['selectedType'] == 'category') {
                $data['category_id'] = $validatedData['selectedData'];
            } elseif ($validatedData['selectedType'] == 'subcategory') {
                $data['sub_category_id'] = $validatedData['selectedData'];
            } elseif ($validatedData['selectedType'] == 'product') {
                $data['product_id'] = $validatedData['selectedData'];
            }

            // Create the discount rule
            DiscountRule::create($data);
    
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Discount Rule Added Successfully')]);
    
        } catch (\Exception $e) {
            // Handle the error
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Error: ' . $e->getMessage())]);
        }
    }

    public function editDiscount(int $id) {
        $this->currentValidation = 'edit';
        $discountRule = DiscountRule::findOrFail($id);
        
        $this->discountRuleId = $id;
        $this->selectedCustomer = $discountRule->customer_id;
        $this->selectedType = $discountRule->type;
        $this->fetchData();
        $this->percentageDiscount = $discountRule->discount_percentage;

        if($discountRule->type == "brand") {
            $this->selectedData = $discountRule->brand_id;
        } else if ($discountRule->type == "category") {
            $this->selectedData = $discountRule->category_id;
        } else if ($discountRule->type == "subcategory") {
            $this->selectedData = $discountRule->sub_category_id;
        } else if ($discountRule->type == "product") {
            $this->selectedData = $discountRule->product_id;
        }
    }

    public function updateDiscount () {
        $validatedData = $this->validate($this->rulesForUpdate());
        try {
                $discountRule = DiscountRule::findOrFail($this->discountRuleId);
                // Prepare the base data
                $data = [
                    'customer_id' => $validatedData['selectedCustomer'],
                    'type' => $validatedData['selectedType'],
                    'discount_percentage' => $validatedData['percentageDiscount'],
                ];
        
                // Conditionally add the correct type ID
                if ($validatedData['selectedType'] == 'brand') {
                    $data['brand_id'] = $validatedData['selectedData'];
                } elseif ($validatedData['selectedType'] == 'category') {
                    $data['category_id'] = $validatedData['selectedData'];
                } elseif ($validatedData['selectedType'] == 'subcategory') {
                    $data['sub_category_id'] = $validatedData['selectedData'];
                } elseif ($validatedData['selectedType'] == 'product') {
                    $data['product_id'] = $validatedData['selectedData'];
                }
    
                // Create the discount rule
                $discountRule->update($data);
        
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Discount Rule Added Successfully')]);

        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
            return;
        }
    
        $this->closeModal();
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Customer Updated Successfully')]);
    }

    // RESET BUTTON
    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
        $this->resetInput();
    }
 
    public function resetInput()
    {
        $this->emit('resetData');
        $this->emit('resetEditData');
    }

    public $discount_selected_id_delete;
    public $discount_name_selected_delete;
    public $showTextTemp;
    public $confirmDelete;
    public function removeDiscount (int $id) {
        $this->discount_selected_id_delete = DiscountRule::find($id);
        $this->discount_name_selected_delete = "Delete";
        if ($this->discount_name_selected_delete) {
            $this->showTextTemp = $this->discount_name_selected_delete;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Record Not Found')]);
        }
    }

    public $discountNameToDelete = '';
    public function destroyDiscount () {
        if ($this->confirmDelete && $this->discountNameToDelete === $this->showTextTemp) {

            DiscountRule::find($this->discount_selected_id_delete->id)->delete();
            $this->closeModal();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Brand Deleted Successfully')]);

            $this->confirmDelete = false;
            $this->discount_selected_id_delete = null;
            $this->discount_name_selected_delete = null;
            $this->discountNameToDelete = '';
            $this->showTextTemp = null;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Operaiton Faild')]);
        }
    }

    // Render
// Render
public function render()
{
    $discountRules = DiscountRule::with(['customer', 'brand', 'category', 'subCategory', 'product']);

    // Apply search term filtering based on type and name translation
    if ($this->statusFilter == 'brands') {
        $discountRules = $discountRules->whereHas('brand.brandtranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        });
    } elseif ($this->statusFilter == 'category') {
        $discountRules = $discountRules->whereHas('category.categoryTranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        });
    } elseif ($this->statusFilter == 'subcategory') {
        $discountRules = $discountRules->whereHas('subCategory.subCategoryTranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        });
    } elseif ($this->statusFilter == 'product') {
        $discountRules = $discountRules->whereHas('product.productTranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        });
    } else {
        // Default search across all related models if no filter is applied
        $discountRules = $discountRules->whereHas('customer.customer_profile', function ($query) {
            $query->where('first_name', 'like', '%' . $this->searchTerm . '%');
        })
        ->orWhereHas('brand.brandtranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->orWhereHas('category.categoryTranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->orWhereHas('subCategory.subCategoryTranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        })
        ->orWhereHas('product.productTranslation', function ($query) {
            $query->where('name', 'like', '%' . $this->searchTerm . '%');
        });
    }

    $discountRules = $discountRules->paginate(10);

    return view('super-admins.pages.customerdiscount.customer-discount', [
        'discountRules' => $discountRules,
    ]);
}

}