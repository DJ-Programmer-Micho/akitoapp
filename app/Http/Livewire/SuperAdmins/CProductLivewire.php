<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Models\Information;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use App\Models\VariationSize;
use Livewire\WithFileUploads;
use App\Models\VariationColor;
use App\Models\ProductVariation;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use App\Models\ProductTranslation;
use App\Models\InformationTranslation;

class CProductLivewire extends Component
{
    use WithFileUploads;


    // Pre-INT
    public $brands;
    public $categoriesData;
    public $tags;
    public $colors;
    public $sizes;
    public $materials;
    public $capacities;
    public $currentValidation = "store";
    // INT
    public $filteredLocales;
    public $glang;
    public $selectedBrand;
    public $products = []; // Product Name
    public $contents = []; // Product Description
    public $productDescriptions = []; // Long Description
    public $productInformations = []; // Addition Description
    public $productShip = []; // Shipping Description
    public $faqs = []; // faqs
    public $selectedCategories = []; // Categories Selected
    public $selectedSubCategories = [];
    public $selectedColors = []; // Colors Selected
    public $selectedMaterials = []; // Materials Selected
    public $selectedSizes = []; // Sizes Selected
    public $selectedCapacities = []; // Capacities Selected
    public $selectedTags = [];
    public $images = []; // Cropped Imgeas
    public $sku; // Cropped Imgeas
    // Sub Form INT
    public $is_spare_part = 0;
    public $is_on_stock = 1;
    public $is_on_sale = 0;
    public $is_featured = 0;
    public $status = 1;
    public $originalPrice;
    public $discountPrice;
    public $discountPercentage;
    public $seoKeywords;


    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        foreach ($this->filteredLocales as $locale) {
            $this->products[$locale] = ''; // or any default value
            $this->contents[$locale] = ''; // or any default value
            $this->productDescriptions[$locale] = ''; // or any default value
            $this->productInformations[$locale] = ''; // or any default value
            $this->productShip[$locale] = ''; // or any default value
        }
    }

    protected function rules()
    {
        //Keep It Empty
    }

    protected function rulesForSaveProduct()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['products.' . $locale] = 'required|string|min:3';
            $rules['contents.' . $locale] = 'required|string|min:15';
            if (!empty($this->faqs)) {
                foreach ($this->faqs as $faqIndex => $faq) {
                    $rules["faqs.$faqIndex.$locale.question"] = 'required|string|min:10';
                    $rules["faqs.$faqIndex.$locale.answer"] = 'required|string|min:10';
                }
            }
        }
        $rules['selectedBrand'] = 'required';
        $rules['originalPrice'] = 'required|numeric|min:0';
        $rules['selectedCategories'] = 'required';
        $rules['selectedSubCategories'] = 'required';
        if ($this->is_on_sale) {
            $rules['discountPrice'] = 'required|numeric|min:0';
        }
        // $rules['priority'] = ['required'];
        // $rules['status'] = ['required'];
        return $rules;
    }

    protected function rulesForUpdateProduct()
    {
        $rules = [];
        foreach ($this->filteredLocales as $locale) {
            $rules['brandsEdit.' . $locale] = 'required|string|min:1';
        }
        $rules['priorityEdit'] = ['required'];
        $rules['statusEdit'] = ['required'];
        return $rules;
    }

    public function updated($propertyName)
    {
    // Validate only the changed property
    if ($this->currentValidation == 'store') {
        $this->validateOnly($propertyName, $this->rulesForSaveProduct());
    } else {
        $this->validateOnly($propertyName, $this->rulesForUpdateProduct());
    }

    // Update discount value when prices change
    if ($propertyName == 'originalPrice' || $propertyName == 'discountPrice') {
        $this->updateDiscountValue();
    }
    }

    public function updateDiscountValue()
    {
        // Cast values to float for accurate calculations
        $originalPrice = (float) $this->originalPrice;
        $discountPrice = (float) $this->discountPrice;

        if ($originalPrice > 0) {
            $this->discountPercentage = (($originalPrice - $discountPrice) / $originalPrice) * 100;
        } else {
            $this->discountPercentage = 0;
        }
    }
    public function toggleCategory($categoryId)
    {
        if (in_array($categoryId, $this->selectedCategories)) {
            // Uncheck category and its subcategories
            $this->selectedCategories = array_filter($this->selectedCategories, fn($id) => $id != $categoryId);
            // Remove all subcategories of this category from the selected subcategories
            $this->selectedSubCategories = array_filter($this->selectedSubCategories, function ($id) use ($categoryId) {
                return !SubCategory::where('category_id', $categoryId)->where('id', $id)->exists();
            });
        } else {
            // Check category and all its subcategories
            $this->selectedCategories[] = $categoryId;
            $subCategories = SubCategory::where('category_id', $categoryId)->pluck('id')->toArray();
            $this->selectedSubCategories = array_merge($this->selectedSubCategories, $subCategories);
        }
    }

    public function toggleSubCategory($subCategoryId, $categoryId)
    {
        if (in_array($subCategoryId, $this->selectedSubCategories)) {
            // Uncheck subcategory
            $this->selectedSubCategories = array_filter($this->selectedSubCategories, fn($id) => $id != $subCategoryId);
            // If no subcategories are left checked, uncheck the main category
            $hasUnchecked = SubCategory::where('category_id', $categoryId)
                ->whereIn('id', $this->selectedSubCategories)
                ->exists();
            if (!$hasUnchecked) {
                $this->selectedCategories = array_filter($this->selectedCategories, fn($id) => $id != $categoryId);
            }
        } else {
            // Check subcategory
            $this->selectedSubCategories[] = $subCategoryId;
            if (!in_array($categoryId, $this->selectedCategories)) {
                $this->selectedCategories[] = $categoryId;
            }
        }
    }
    public function initialLoad(){
        $this->brands = Brand::with(['brandtranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->categoriesData = Category::with([
            'subCategory' => function ($query) {
                $query->with(['subCategoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                }])->orderBy('priority');
            },
            'categoryTranslation' => function ($query) {
                $query->where('locale', app()->getLocale());
            }
        ])->orderBy('priority')->get();

        $this->tags = Tag::with(['tagtranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->colors = VariationColor::with(['variationColorTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->materials = VariationMaterial::with(['variationMaterialeTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->sizes = VariationSize::with(['variationSizeTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        $this->capacities = VariationCapacity::with(['variationCapacityTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();
    }

     // CRUD Handler
    public function addColorSelect()
    {
        $this->selectedColors[] = ['color_id' => null]; // Add a new empty selection
    }

     public function removeColorSelect($index)
     {
         unset($this->selectedColors[$index]); // Remove the specific row
         $this->selectedColors = array_values($this->selectedColors); // Re-index the array
     }

     public function addMaterialSelect()
     {
         $this->selectedMaterials[] = ['material_id' => null]; // Add a new empty selection
     }
     public function removeMaterialSelect($index)
     {
         unset($this->selectedMaterials[$index]); // Remove the specific row
         $this->selectedMaterials = array_values($this->selectedMaterials); // Re-index the array
     }

     public function addSizeSelect()
     {
         $this->selectedSizes[] = ['size_id' => null]; // Add a new empty selection
     }
     public function removeSizeSelect($index)
     {
         unset($this->selectedSizes[$index]); // Remove the specific row
         $this->selectedSizes = array_values($this->selectedSizes); // Re-index the array
     }

     public function addCapacitySelect()
     {
         $this->selectedCapacities[] = ['capacity_id' => null]; // Add a new empty selection
     }
     public function removeCapacitySelect($index)
     {
         unset($this->selectedCapacities[$index]); // Remove the specific row
         $this->selectedCapacities = array_values($this->selectedCapacities); // Re-index the array
     }

    // Add a new FAQ entry
    public function addFaq()
    {
        // Initialize an empty FAQ for each locale
        $newFaq = [];
        foreach ($this->filteredLocales as $locale) {
            $newFaq[$locale] = ['question' => '', 'answer' => ''];
        }
        $this->faqs[] = $newFaq; // Add the new FAQ with localized fields
    }
    // Remove an FAQ entry
    public function removeFaq($index)
    {
        unset($this->faqs[$index]); // Remove the specific FAQ
        $this->faqs = array_values($this->faqs); // Re-index the array
    }

    public function upload()
    {
        foreach ($this->images as $image) {
            $path = $image->store('uploads', 'public');
            // Here you can process the image, like cropping
        }

        // Clear the images after upload
        $this->reset('images');

        session()->flash('message', 'Images successfully uploaded.');
    }

    public function removeImage($index)
    {
        unset($this->images[$index]);
    }

    
    public function productSave(){

        $this->currentValidation = 'store';
        $validatedData = $this->validate($this->rulesForSaveProduct());


        $Information = Information::create([
            'created_by_id' => 1,
            'updated_by_id' => 1,
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            InformationTranslation::create([
                'product_id' => $Information->id,
                'locale' => $locale,
                'description' => $this->productDescriptions[$locale],
                'addition' => $this->productInformations[$locale],
                'shipping' => $this->productShip[$locale],
                'question_and_answer' => $this->faqs[$locale],
            ]);
        }


        $variation = ProductVariation::create([
            // 'product_id' => $this->status ?? 1,
            'color_id' => $this->selectedColors ?? null,
            'size_id' => $this->selectedSizes ?? null,
            'material_id' => $this->selectedMaterials ?? null,
            'capacity_id' => $this->status ?? null,
            'sku' => $this->sku ?? null,
            'price' => $this->originalPrice ?? null,
            'discount_price' => $this->discountPrice ?? null,
            'on_stock' => $this->is_on_stock ?? 1,
            'on_sale' => $this->is_on_sale ?? 0,
            'featured' => $this->is_featured ?? 0,
            'status' => $this->status ?? 1,
            'priority' => $this->status ?? 3,
        ]);

        $product = Product::create([
            'created_by_id' => 1,
            'updated_by_id' => 1,
            'brand_id' => $validatedData['selectedBrand'],
            'category_id' => $validatedData['selectedCategories'],
            'sub_category_id' => $validatedData['selectedSubCategories'],
            // 'slug_id' => $validatedData['brand'],
            'tag_id' => $validatedData['selectedTags'],
            'variation_id' => $variation->id,
            'information_id' => $Information->id,
            'is_spare_part' => $this->is_spare_part ?? 0,
            'priority' => 1,
            'status' => $this->status ?? 1,
        ]);
    
        foreach ($this->filteredLocales as $locale) {
            ProductTranslation::create([
                'product_id' => $product->id,
                'locale' => $locale,
                'name' => $this->products[$locale],
                'description' => $this->contents[$locale],
                'slug' => Str::slug($this->products[$locale], '-', ''),
            ]);
        }




    
        $product = ProductImage::create([
            'variation_id' => $this->selectedColors ?? null,
            'image_path' => $this->selectedSizes ?? null,
            'is_primary' => $this->selectedMaterials ?? null,
            'is_secondary' => $this->status ?? null,
            'priority' => $this->sku ?? null,
        ]);
    }


     // Render
     public function render(){ 
        $this->initialLoad();
        return view('super-admins.pages.cproducts.product-form', [
        ]);
    }

}