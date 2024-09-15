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
use Illuminate\Validation\Rule;
use App\Models\ProductVariation;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use App\Models\ProductTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\InformationTranslation;
use Illuminate\Support\Facades\Storage;

class EProductLivewire extends Component
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
    public $keywords; // Cropped Imgeas
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
    
    public $p_id;
    protected $listeners = ['fileProcessed', 'filesReordered', 'removeImage'];

    // On Load
    public function mount($id){
        $this->p_id = $id;
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');
        foreach ($this->filteredLocales as $locale) {
            $this->products[$locale] = ''; // or any default value
            $this->contents[$locale] = ''; // or any default value
            $this->productDescriptions[$locale] = ''; // or any default value
            $this->productInformations[$locale] = ''; // or any default value
            $this->productShip[$locale] = ''; // or any default value
        }
        Log::info('Locale before initialLoad: ' . app()->getLocale());
        $this->initialLoad();
        Log::info('Locale after initialLoad: ' . app()->getLocale());
        
        $this->loadProductData();
        Log::info('Locale after loadProductData: ' . app()->getLocale());
        
    }

    protected function loadProductData()
    {
        $faqs = [];
        // Fetch the product with all related data
        $product = Product::with([
            'variation.colors', 
            'variation.materials', 
            'variation.sizes', 
            'variation.capacities', 
            'variation.images', 
            'variation', 
            'information.informationTranslation', 
            'productTranslation', 
            'categories', 
            'subCategories', 
            'tags'
        ])->findOrFail($this->p_id);
        $existingImages = $product->variation->images->map(function ($image) {
            $tempFileName = basename($image->image_path);
            $tempFilePath = 'livewire-tmp/' . $tempFileName;
        
            try {
                // Fetch image from S3
                $s3Contents = Storage::disk('s3')->get($image->image_path);
                // Save the file to temporary local storage
                Storage::disk('local')->put($tempFilePath, $s3Contents);
                // Generate the temporary URL to serve the image
                $temporaryUrl = route('temp-images', ['filename' => $tempFileName]);
            } catch (\Exception $e) {
                Log::error("Failed to fetch or store the image: {$image->image_path}, error: {$e->getMessage()}");
                $temporaryUrl = null;
            }
        
            return (object)[
                'id' => $image->id,
                'tempFileName' => $tempFileName,
                'temporaryUrl' => $temporaryUrl,
                'is_existing' => true, // Mark the image as an existing one
                'priority' => $image->priority, // Ensure priority is carried over for reordering
                'is_removed' => false
            ];
        })->toArray();
        // Set the images to be rendered in Livewire
        $this->images = $existingImages;

        foreach ($this->filteredLocales as $locale) {
            $this->products[$locale] = $product->productTranslation->where('locale', $locale)->first()->name ?? null;
            $this->contents[$locale] = $product->productTranslation->where('locale', $locale)->first()->description ?? null;
            $this->productDescriptions[$locale] = $product->information->informationTranslation->where('locale', $locale)->first()->description ?? null;
            $this->productInformations[$locale] = $product->information->informationTranslation->where('locale', $locale)->first()->addition ?? null;
            $this->productShip[$locale] = $product->information->informationTranslation->where('locale', $locale)->first()->shipping ?? null;
            // Get the relevant informationTranslation for the locale
  
            // Fetch the translation for the current locale
            $translation = $product->information->informationTranslation->where('locale', $locale)->first();
        
            if ($translation) {
                // Decode the JSON data stored in 'question_and_answer' for the current locale
                $faqData = json_decode($translation->question_and_answer, true);
                // Handle JSON decode errors
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $faqData = [];
                }
                // Populate the $faqs array by index, and store each locale's question/answer under the index
                foreach ($faqData as $index => $data) {
                    $faqs[$index][$locale] = [
                        'question' => $data['question'] ?? '',
                        'answer' => $data['answer'] ?? ''
                    ];
                }
            } else {
                foreach ($faqs as $index => $faq) {
                    $faqs[$index][$locale] = ['question' => '', 'answer' => ''];
                }
            }
            // Assign the structured $faqs array to the Livewire component property for rendering
            $this->faqs = $faqs;
        }
        // Load variations
        $this->sku = $product->variation->sku;
        $this->keywords = $product->variation->keywords;
        $this->originalPrice = $product->variation->price;
        $this->discountPrice = $product->variation->discount;
        $this->is_on_stock = $product->variation->on_stock;
        $this->is_on_sale = $product->variation->on_sale;
        $this->is_featured = $product->variation->featured;
        $this->is_spare_part = $product->is_spare_part;
        $this->status = $product->status;
        $this->selectedCategories = $product->categories->pluck('id')->toArray();
        $this->selectedSubCategories = $product->subCategories->pluck('id')->toArray();
        $this->selectedTags = $product->tags->pluck('id')->toArray();
        $this->selectedBrand = $product->brand_id;

        // Initialize selected variations
        $this->selectedColors = $product->variation->colors->map(function ($color) {
            return ['color_id' => $color->id];
        })->toArray();

        $this->selectedMaterials = $product->variation->materials->map(function ($material) {
            return ['material_id' => $material->id];
        })->toArray();

        $this->selectedSizes = $product->variation->sizes->map(function ($size) {
            return ['size_id' => $size->id];
        })->toArray();

        $this->selectedCapacities = $product->variation->capacities->map(function ($capacity) {
            return ['capacity_id' => $capacity->id];
        })->toArray();
    }
    
    protected function rules()
    {
        //Keep It Empty
    }

    protected function rulesForSaveProduct()
    {
        $rules = [];
        $productNames = collect($this->products);
        
        foreach ($this->filteredLocales as $locale) {
            $rules['products.' . $locale] = [
                'required',
                'string',
                'min:3',
                Rule::unique('product_translations', 'name')
                    ->where('locale', $locale)
                    ->ignore($this->p_id, 'product_id')
            ];

            $rules['products.' . $locale][] = function ($attribute, $value, $fail) use ($productNames, $locale) {
                foreach ($this->filteredLocales as $otherLocale) {
                    if ($locale !== $otherLocale && $productNames->get($locale) === $productNames->get($otherLocale)) {
                        $fail(__('The :attribute must be unique across different languages.'));
                    }
                }
            };


            $rules['contents.' . $locale] = 'required|string|min:15';
            if (!empty($this->faqs)) {
                foreach ($this->faqs as $faqIndex => $faq) {
                    $rules["faqs.$faqIndex.$locale.question"] = 'required|string|min:10';
                    $rules["faqs.$faqIndex.$locale.answer"] = 'required|string|min:10';
                }
            }
        }
        $rules['images'] = 'required';
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

        $this->materials = VariationMaterial::with(['variationMaterialTranslation' => function ($query) {
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

    // public function removeImage($index)
    // {
    //     unset($this->images[$index]);
    // }

    public function fileProcessed($imagesData)
    {
        // Handle the updated image data (existing and new files) passed from the frontend
        $this->images = $imagesData;
    }
    
    public function removeImage($temporaryUrl)
    {
        // Update images to mark the image as removed
        $this->images = collect($this->images)->map(function ($image) use ($temporaryUrl) {
            // Check if the temporaryUrl matches
            if ($image['file']['temporaryUrl'] === $temporaryUrl) {
                $image['is_removed'] = true; // Mark as removed
            }
            return $image;
        })->toArray();
        
        // Optionally, emit an event or perform additional logic if needed
        $this->emit('imageRemoved');
    }
    
    
    public function filesReordered($orderedImages)
    {
        // Convert $orderedImages to an associative array keyed by 'file.temporaryUrl' for quick lookup
        $orderedImagesMap = [];
        foreach ($orderedImages as $orderedImage) {
            $temporaryUrl = $orderedImage['file']['temporaryUrl'] ?? null;
            if ($temporaryUrl) {
                $orderedImagesMap[$temporaryUrl] = $orderedImage;
            }
        }
        
        // Iterate through each image in $this->images
        foreach ($this->images as &$image) {
            $temporaryUrl = $image['file']['temporaryUrl'] ?? null;
            if ($temporaryUrl && isset($orderedImagesMap[$temporaryUrl])) {
                // Update the priority based on the reordered image
                $image['priority'] = $orderedImagesMap[$temporaryUrl]['priority'];
            }
        }
        
        // Ensure to unset the reference for clean array
        unset($image);
    
        // Convert the updated array back to a simple array
        $this->images = array_values($this->images);
    }
    
    
    public function productSave()
    {
        // foreach ($this->images as $index => $image) {
        //     dd($image['file']);
        // }
        // dd($this->images, $this->imagesData);

        $this->currentValidation = 'store';
        $validatedData = $this->validate($this->rulesForSaveProduct());
        // Start database transaction
        DB::beginTransaction();
    
        try {
            $product = Product::findOrFail($this->p_id);
            $variation = $product->variation;
            $information = $product->information;
            // Create Information entry
            if($this->productDescriptions || $this->productInformations || $this->productShip || $this->faqs) {

                
                if ($information) {
                    $information->update([
                        'updated_by_id' => 1,
                    ]);
                } else {
                    // Create new information if it doesn't exist
                    $information = Information::create([
                        'created_by_id' => 1,
                        'updated_by_id' => 1,
                    ]);
                }
        
                foreach ($this->filteredLocales as $locale) {
                    InformationTranslation::updateOrCreate(
                        ['information_id' => $information->id, 'locale' => $locale],
                        [
                            'description' => $this->productDescriptions[$locale],
                            'addition' => $this->productInformations[$locale],
                            'shipping' => $this->productShip[$locale],
                            'question_and_answer' => $this->faqs[$locale] ?? null,
                        ]
                    );
                }
            }

            // Update  Variation entry
            $variation->update([
                'sku' => $this->sku ?? $variation->sku,
                'keywords' => $this->keywords ?? $variation->keywords,
                'price' => $this->originalPrice ?? $variation->price,
                'discount' => $this->discountPrice ?? $variation->discount,
                'on_stock' => $this->is_on_stock ?? $variation->on_stock,
                'on_sale' => $this->is_on_sale ?? $variation->on_sale,
                'featured' => $this->is_featured ?? $variation->featured,
            ]);


            $variation->colors()->sync(array_column($this->selectedColors, 'color_id') ?? []);
            $variation->sizes()->sync(array_column($this->selectedSizes, 'size_id') ?? []);
            $variation->materials()->sync(array_column($this->selectedMaterials, 'material_id') ?? []);
            $variation->capacities()->sync(array_column($this->selectedCapacities, 'capacity_id') ?? []);

            // Create Product entry
            $product->update([
                'updated_by_id' => 1,
                'brand_id' => $validatedData['selectedBrand'] ?? $product->brand_id,
                'information_id' => $information->id ?? $product->information_id,
                'is_spare_part' => $this->is_spare_part ?? $product->is_spare_part,
                'priority' => 1,
                'status' => $this->status ?? $product->status,
            ]);

            $product->categories()->sync($validatedData['selectedCategories'] ?? []);
            $product->subCategories()->sync($validatedData['selectedSubCategories'] ?? []);
            $product->tags()->sync($this->selectedTags ?? []);
    
            // Update or create product translations
            foreach ($this->filteredLocales as $locale) {
                ProductTranslation::updateOrCreate(
                    ['product_id' => $product->id, 'locale' => $locale],
                    [
                        'name' => $this->products[$locale],
                        'description' => $this->contents[$locale],
                        'slug' => Str::slug($this->products[$locale], '-', ''),
                    ]
                );
            }
    
            // Upload images to S3 and create ProductImage entries
            // foreach ($this->images as $index => $image) {
            //     // Generate a unique name for each image
            //     $microtime = str_replace('.', '', microtime(true));
            //     $fileName = 'products/' . ($this->products['en'] ?? 'products') . '_product_' . date('Ydm') . $microtime . '.' . $image->extension();
    
            //     try {
            //         // Store the image to S3
            //         $imagePath = $image->storeAs('products', $fileName, 's3');
                    
            //         // Store image data in the database
            //         ProductImage::create([
            //             'variation_id' => $variation->id,
            //             'image_path' => $imagePath, // S3 image path
            //             'is_primary' => $index === 0 ? true : false, // Set first image as primary
            //             'is_secondary' => $index === 1 ? true : false, // Set second image as secondary
            //             'priority' => $index, // Store the index as priority
            //         ]);
            //     } catch (\Exception $e) {
            //         // Rollback transaction if an error occurs
            //         DB::rollBack();
    
            //         $this->dispatchBrowserEvent('alert', [
            //             'type' => 'error',
            //             'message' => __('Failed to upload image to S3: ' . $fileName . ' - ' . $e->getMessage())
            //         ]);
    
            //         return;
            //     }
            // }

            foreach ($this->images as $index => $image) {
                // Check if the image is marked as removed
                if ($image['is_removed']) {
                    // Delete the image from S3 and the database
                    if ($image['is_existing']) {
                        // dd($image['tempFileName'], $image);
                        try {
                            // Check if the file exists on S3 before attempting to delete
                            if (Storage::disk('s3')->exists('products/products/'.$image['tempFileName'])) {
                                Storage::disk('s3')->delete('products/products/'.$image['tempFileName']);
                            }
                        } catch (\Exception $e) {
                            $this->dispatchBrowserEvent('alert', [
                                'type' => 'error',
                                'message' => __('Failed to delete image from S3: ' . $e->getMessage())
                            ]);
                        }
            
                        // Remove from the database
                        ProductImage::where('id', $image['id'])->delete();
                    }
                    continue; // Skip further processing for removed images
                }
            
                if ($image['is_existing']) {
                    // Update the priority for existing images
                    ProductImage::where('id', $image['id'])->update([
                        'priority' => $image['priority'], // Update priority
                        'is_primary' => $image['priority'] === 0 ? true : false,
                        'is_secondary' => $image['priority'] === 1 ? true : false,
                    ]);
                
                } else {
                    // Handle new uploaded images
                    $microtime = str_replace('.', '', microtime(true));
                    $extension = pathinfo($image['file']['temporaryUrl'], PATHINFO_EXTENSION);
                    $fileName = 'products/' . ($this->products['en'] ?? 'products') . '_product_' . date('Ydm') . $microtime . '.' . $extension;
                    $localTempPath = storage_path('app/livewire-tmp/' . $image['file']['temporaryUrl']);

                    try {
                        if (file_exists($localTempPath)) {
                            // Read the file contents
                            $fileContents = file_get_contents($localTempPath);
                            
                            if ($fileContents === false) {
                                throw new \Exception('Failed to read file contents.');
                            }
            
                            // Upload the new image to S3
                            $imagePath = Storage::disk('s3')->put('products/' . $fileName, $fileContents);
            
                            // Clean up the temporary local file
                            // unlink($localTempPath);
                            // Insert the new image into the database
                           
                            ProductImage::create([
                                'variation_id' => $variation->id,
                                'image_path' => 'products/'.$fileName, // S3 image path
                                'is_primary' => $image['priority'] === 0 ? true : false,
                                'is_secondary' => $image['priority'] === 1 ? true : false,
                                'priority' => $image['priority'],
                            ]);
                        } else {
                            throw new \Exception('File is not a valid instance of UploadedFile.');
                            return;
                        }
                    } catch (\Exception $e) {
                        $this->dispatchBrowserEvent('alert', [
                            'type' => 'error',
                            'message' => __('Failed to handle image: ' . $e->getMessage())
                        ]);
                        return;
                    }
                }
            }
            
    
            // Commit transaction
            DB::commit();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Product Added Successfully')]);
            // Clear the images after upload
            $this->resetInputValues();


            try {
                $files = Storage::files('livewire-tmp');

                foreach ($files as $file) {
                    Storage::delete($file);
                }
                $this->dispatchBrowserEvent('clean-image');

            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'warning',  'message' => __('IMage Cache Did Not Clear ' . $e)]);

            }

            // return redirect()->route('super.product.table', ['locale' => app()->getLocale()]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Something Went Wrong ' . $e)]);
            DB::rollBack();
            // Rollback transaction if an error occurs
    
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Failed to save product: ' . $e->getMessage())
            ]);
        }
    }
    public function resetInputValues() {
        $this->selectedBrand = "";
        $this->faqs = []; // faqs
        $this->selectedCategories = []; // Categories Selected
        $this->selectedSubCategories = [];
        $this->selectedColors = []; // Colors Selected
        $this->selectedMaterials = []; // Materials Selected
        $this->selectedSizes = []; // Sizes Selected
        $this->selectedCapacities = []; // Capacities Selected
        $this->selectedTags = [];
        $this->images = []; // Cropped Imgeas
        $this->sku = ""; // Cropped Imgeas
        $this->keywords = ""; // Cropped Imgeas
        $this->is_spare_part = 0;
        $this->is_on_stock = 1;
        $this->is_on_sale = 0;
        $this->is_featured = 0;
        $this->status = 1;
        $this->originalPrice = '';
        $this->discountPrice = "";
        $this->discountPercentage = "";
        foreach ($this->filteredLocales as $locale) {
            $this->products[$locale] = ''; // or any default value
            $this->contents[$locale] = ''; // or any default value
            $this->productDescriptions[$locale] = ''; // or any default value
            $this->productInformations[$locale] = ''; // or any default value
            $this->productShip[$locale] = ''; // or any default value
        }


    }
    

     // Render
     public function render(){ 
        
        // $this->initialLoad();
        return view('super-admins.pages.eproducts.product-form');
    }

}