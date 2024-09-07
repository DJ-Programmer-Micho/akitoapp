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
use Illuminate\Support\Facades\DB;
use App\Models\InformationTranslation;
use Illuminate\Support\Facades\Storage;

use function PHPUnit\Framework\isEmpty;

class TProductLivewire extends Component
{
    use WithFileUploads;



    public $brandIds = [];
    public $categoryIds = [];
    public $subCategoryIds = [];
    public $sizeIds = [];
    public $colorIds = [];
    public $capacityIds = [];
    public $materialIds = [];
    public $minPrice = 0;
    public $maxPrice = 1000;
    public $sortBy = 'priority';
    public $grid = 4;

    protected $queryString = [
        'brandIds',
        'categoryIds',
        'subCategoryIds',
        'sizeIds',
        'colorIds',
        'capacityIds',
        'materialIds',
        'minPrice',
        'maxPrice',
        'sortBy',
        'grid',
        'statusFilter', 'page'
    ];

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
    // Render
    public $search = '';
    public $statusFilter = 'all';
    public $page = 1;
    public $activeCount = 0;
    public $nonActiveCount = 0;
    public $de = 0;

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

    public function productSave()
    {

        $this->currentValidation = 'store';
        $validatedData = $this->validate($this->rulesForSaveProduct());
        // Start database transaction
        DB::beginTransaction();
    
        try {
            // Create Information entry
            if($this->productDescriptions || $this->productInformations || $this->productShip || $this->faqs) {

                
                $information = Information::create([
                    'created_by_id' => 1,
                    'updated_by_id' => 1,
                ]);

        
                foreach ($this->filteredLocales as $locale) {
                    InformationTranslation::create([
                        'information_id' => $information->id,
                        'locale' => $locale,
                        'description' => $this->productDescriptions[$locale],
                        'addition' => $this->productInformations[$locale],
                        'shipping' => $this->productShip[$locale],
                        'question_and_answer' => $this->faqs[$locale] ?? null,
                    ]);
                }
            }

            // Create Variation entry
            $variation = ProductVariation::create([
                'sku' => $this->sku ?? null,
                'price' => $this->originalPrice ?? null,
                'discount' => $this->discountPrice ?? null,
                'on_stock' => $this->is_on_stock ?? 1,
                'on_sale' => $this->is_on_sale ?? 0,
                'featured' => $this->is_featured ?? 0,
            ]);

            // Attach colors if any are selected
            if (!empty($this->selectedColors)) {
                $variation->colors()->attach(array_column($this->selectedColors, 'color_id'));
            }

            // Attach sizes if any are selected
            if (!empty($this->selectedSizes)) {
                $variation->sizes()->attach(array_column($this->selectedSizes, 'size_id'));
            }

            // Attach materials if any are selected
            if (!empty($this->selectedMaterials)) {
                $variation->materials()->attach(array_column($this->selectedMaterials, 'material_id'));
            }

            // Attach capacities if any are selected
            if (!empty($this->selectedCapacities)) {
                $variation->capacities()->attach(array_column($this->selectedCapacities, 'capacity_id'));
            }
    
          
            // Create Product entry
            $product = Product::create([
                'created_by_id' => 1,
                'updated_by_id' => 1,
                'brand_id' => $validatedData['selectedBrand'],
                'variation_id' => $variation->id,
                'information_id' => $information->id ?? null,
                'is_spare_part' => $this->is_spare_part ?? 0,
                'priority' => 1,
                'status' => $this->status ?? 1,
            ]);

            if (!empty($validatedData['selectedCategories'])) {
                $product->categories()->attach($validatedData['selectedCategories']);
            }

            if (!empty($validatedData['selectedSubCategories'])) {
                $product->subCategories()->attach($validatedData['selectedSubCategories']);
            }

            if (!empty($this->selectedTags)) {
                $product->tags()->attach($this->selectedTags);
            }

            foreach ($this->filteredLocales as $locale) {
                ProductTranslation::create([
                    'product_id' => $product->id,
                    'locale' => $locale,
                    'name' => $this->products[$locale],
                    'description' => $this->contents[$locale],
                    'slug' => Str::slug($this->products[$locale], '-', ''),
                ]);
            }
    
            // Upload images to S3 and create ProductImage entries
            foreach ($this->images as $index => $image) {
                // Generate a unique name for each image
                $microtime = str_replace('.', '', microtime(true));
                $fileName = 'products/' . ($this->products['en'] ?? 'products') . '_product_' . date('Ydm') . $microtime . '.' . $image->extension();
    
                try {
                    // Store the image to S3
                    $imagePath = $image->storeAs('products', $fileName, 's3');
                    
                    // Store image data in the database
                    ProductImage::create([
                        'variation_id' => $variation->id,
                        'image_path' => $imagePath, // S3 image path
                        'is_primary' => $index === 0 ? true : false, // Set first image as primary
                        'is_secondary' => $index === 1 ? true : false, // Set second image as secondary
                        'priority' => $index, // Store the index as priority
                    ]);
                } catch (\Exception $e) {
                    // Rollback transaction if an error occurs
                    DB::rollBack();
    
                    $this->dispatchBrowserEvent('alert', [
                        'type' => 'error',
                        'message' => __('Failed to upload image to S3: ' . $fileName . ' - ' . $e->getMessage())
                    ]);
    
                    return;
                }
            }
    
            // Commit transaction
            DB::commit();
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Page Loaded Added Successfully')]);
            // Clear the images after upload
            $this->reset('images');
            $this->resetInputValues();
    
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Page Loaded Added Successfully')]);
            DB::rollBack();
            // Rollback transaction if an error occurs
    
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Failed to save product: ' . $e->getMessage())
            ]);
        }
        $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Page Loaded Added Successfully')]);

    }




        
    public function render()
    {
        $productQuery = Product::select('products.*', 'product_variations.price as variation_price')
            ->join('product_variations', 'products.variation_id', '=', 'product_variations.id')
            ->with([
                'productTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'variation.colors',
                'variation.sizes',
                'variation.materials',
                'variation.capacities',
                'variation.images',
                'brand.brandtranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'categories.categoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'tags.tagTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
                'subCategories.subCategoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                },
            ])
            ->where('products.status', 1)
            ->where('products.is_spare_part', 0);

        $productQuery->when($this->brandIds, function ($query, $brandIds) {
            return $query->whereIn('products.brand_id', $brandIds);
        })
        ->when($this->categoryIds, function ($query, $categoryIds) {
            return $query->whereHas('categories', function ($q) use ($categoryIds) {
                $q->whereIn('category_id', $categoryIds);
            });
        })
        ->when($this->subCategoryIds, function ($query, $subCategoryIds) {
            return $query->whereHas('subCategories', function ($q) use ($subCategoryIds) {
                $q->whereIn('sub_category_id', $subCategoryIds);
            });
        })
        ->when($this->sizeIds, function ($query, $sizeIds) {
            return $query->whereHas('variation.sizes', function ($q) use ($sizeIds) {
                $q->whereIn('variation_size_id', $sizeIds);
            });
        })
        ->when($this->colorIds, function ($query, $colorIds) {
            return $query->whereHas('variation.colors', function ($q) use ($colorIds) {
                $q->whereIn('variation_color_id', $colorIds);
            });
        })
        ->when($this->capacityIds, function ($query, $capacityIds) {
            return $query->whereHas('variation.capacities', function ($q) use ($capacityIds) {
                $q->whereIn('variation_capacity_id', $capacityIds);
            });
        })
        ->when($this->materialIds, function ($query, $materialIds) {
            return $query->whereHas('variation.materials', function ($q) use ($materialIds) {
                $q->whereIn('variation_material_id', $materialIds);
            });
        })
        ->when([$this->minPrice, $this->maxPrice], function ($query) {
            return $query->whereBetween('product_variations.price', [$this->minPrice, $this->maxPrice]);
        });

        $products = $productQuery->paginate(12);

        // Fetch filters data
        $this->brands = Brand::whereIn('id', $products->pluck('brand_id')->unique())
            ->with('brandTranslation')
            ->get();

        $categories = Category::whereIn('id', $products->flatMap(function ($product) {
            return $product->categories->pluck('id');
        })->unique())->with('categoryTranslation')->get();

        $subCategories = SubCategory::whereIn('id', $products->flatMap(function ($product) {
            return $product->subCategories->pluck('id');
        })->unique())->with('subCategoryTranslation')->get();

        $this->sizes = VariationSize::whereIn('id', $products->flatMap(function ($product) {
            return optional($product->variation)->sizes ? $product->variation->sizes->pluck('id') : collect();
        })->unique())->with('variationSizeTranslation')->get();
        
        $this->colors = VariationColor::whereIn('id', $products->flatMap(function ($product) {
            return optional($product->variation)->colors ? $product->variation->colors->pluck('id') : collect();
        })->unique())->get();
        
        $this->capacities = VariationCapacity::whereIn('id', $products->flatMap(function ($product) {
            return optional($product->variation)->capacities ? $product->variation->capacities->pluck('id') : collect();
        })->unique())->with('variationCapacityTranslation')->get();
        
        $this->materials = VariationMaterial::whereIn('id', $products->flatMap(function ($product) {
            return optional($product->variation)->materials ? $product->variation->materials->pluck('id') : collect();
        })->unique())->with('variationMaterialeTranslation')->get();




        return view('super-admins.pages.tproducts.product-table', [
            'tableData' => $products,
            'categories' => $categories,
            'subCategories' => $subCategories,
            'minPrice' => $this->minPrice,
            'maxPrice' => $this->maxPrice,
            'grid' => $this->grid,
        ]);
    }

}