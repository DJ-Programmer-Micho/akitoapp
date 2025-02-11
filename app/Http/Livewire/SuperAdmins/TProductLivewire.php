<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Product;
use Livewire\Component;
use App\Models\Category;
use App\Models\SubCategory;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Livewire\WithPagination;
use App\Models\VariationSize;
use App\Models\VariationColor;
use App\Models\ProductVariation;
use App\Models\VariationCapacity;
use App\Models\VariationMaterial;
use App\Models\ProductTranslation;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class TProductLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    public $brandIds = [];
    public $categoryIds = [];
    public $subCategoryIds = [];
    public $sizeIds = [];
    public $colorIds = [];
    public $capacityIds = [];
    public $materialIds = [];
    public $minPrice = 0;
    public $maxPrice = 5000;
    public $sortBy = 'priority';
    public $items = 10;
    public $activeCount;
    public $nonActiveCount;

    // Pre-INT
    public $brands;
    public $categoriesData;
    public $tags;
    public $colors;
    public $sizes;
    public $materials;
    public $capacities;
    public $selectedColors = [];

    // Filter and Search
    public $search = '';
    public $statusFilter = 'all';
    public $page = 1;
    public $glang;


    public function mount()
    {
        $this->glang = app()->getLocale();
        $this->loadInitialData();
    }

    public function loadInitialData()
    {
        // Load all brands
        $this->brands = Brand::with(['brandtranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        // Load all categories and subcategories
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

        // Load all tags
        $this->tags = Tag::with(['tagtranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->get();

        // Load all colors, sizes, materials, capacities
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

    // CRUD HANDLER
    public function updateStatus(int $id)
    {
        // Find the brand by ID, if not found return an error
        $brandStatus = Product::find($id);
    
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

    public function updatePriority(int $p_id, $updatedPriority)
    {
        // Validate if updatedPriority is a number
        if (!is_numeric($updatedPriority)) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Invalid priority value')
            ]);
            return;
        }
    
        // Find the brand by ID
        $brand = Product::find($p_id);
        
        if ($brand) {
            $brand->priority = $updatedPriority;
            $brand->save();
            
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',  
                'message' => __('Priority Updated Successfully')
            ]);
        } else {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',  
                'message' => __('Record Not Found')
            ]);
        }
    }


    public function toggleColor($colorId)
    {
        if (in_array($colorId, $this->selectedColors)) {
            // Remove the color if it's already selected
            $this->selectedColors = array_diff($this->selectedColors, [$colorId]);
        } else {
            // Add the color if it's not selected
            $this->selectedColors[] = $colorId;
        }
    
        // Trigger reactivity by calling render
        $this->filterProducts();
    }
    
    public function filterProducts()
    {
        // Re-render the component to apply filters
        $this->render();
    }
    
    public function statusFilter($status)
    {
        $this->statusFilter = $status;
    }

    public function changeTab($status)
    {
        $this->statusFilter = $status;
        $this->page = 1;
    }

    // DELETE
    public $product_selected_id_delete;
    public $product_name_selected_delete;
    public $productNameToDelete;
    public $showTextTemp;
    public $confirmDelete;
    public function removeProduct(int $id)
    {
        $this->product_selected_id_delete = Product::find($id);
        $this->product_name_selected_delete = ProductTranslation::where('product_id', $id)
            ->where('locale', app()->getLocale())
            ->first() ?? "Delete";

        if ($this->product_name_selected_delete) {
            $this->showTextTemp = $this->product_name_selected_delete->name;
            $this->confirmDelete = true;
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Record Not Found')]);
        }
    }

    public function destroyProduct()
    {
        if ($this->confirmDelete && $this->productNameToDelete === $this->showTextTemp) {
            try {
                // First, delete variations and their associated images
                $variations = ProductVariation::where('id', $this->product_selected_id_delete->variation_id)->get();
    
                foreach ($variations as $variation) {
                    // Retrieve associated images for each variation
                    $images = ProductImage::where('variation_id', $variation->id)->get();
    
                    foreach ($images as $image) {
                        // Delete image from S3
                        if ($image->image_path) {
                            Storage::disk('s3')->delete($image->image_path);
                        }
                        // Now delete the image record itself
                        $image->delete();
                    }
    
                    // Now delete the variation itself
                    $variation->delete();
                }
    
                // Delete the main product image
                if ($this->product_selected_id_delete->image) {
                    Storage::disk('s3')->delete($this->product_selected_id_delete->image);
                }
    
                // Finally, delete the product itself
                $this->product_selected_id_delete->delete();
                $this->closeModal();
                $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Product Deleted Successfully')]);
    
                // Reset the confirmation and other properties
                $this->confirmDelete = false;
                $this->product_selected_id_delete = null;
                $this->product_name_selected_delete = null;
                $this->productNameToDelete = '';
                $this->showTextTemp = null;
    
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Try Reload the Page: ' . $e->getMessage())]);
                return;
            }
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('Operation Failed')]);
        }
    }
    
    public function closeModal()
    {
        $this->dispatchBrowserEvent('close-modal');
    }
 
    public function render()
    {
        // Calculate active and non-active counts
        $this->activeCount = Product::where('status', 1)->count();
        $this->nonActiveCount = Product::where('status', 0)->count();
        
        // Build the product query
        $productsQuery = Product::query()
        ->distinct()
        ->join('product_translations', 'products.id', '=', 'product_translations.product_id')
        ->select('products.*', 'product_translations.name as product_name')
        ->where('product_translations.locale', $this->glang)
    
        // Apply search filter
        ->when($this->search, function ($query) {
            $query->where('product_translations.name', 'like', '%' . $this->search . '%');
        })
    
            // Apply status filter
            ->when($this->statusFilter === 'active', function ($query) {
                $query->where('status', 1);
            })
            ->when($this->statusFilter === 'non-active', function ($query) {
                $query->where('status', 0);
            })
            // Apply brand filter
            ->when(!empty($this->brandIds), function ($query) {
                $query->whereIn('products.brand_id', $this->brandIds);
            })
    
            // Apply category filter
            ->when(!empty($this->categoryIds), function ($query) {
                $query->whereHas('categories', function ($query) {
                    $query->whereIn('categories.id', $this->categoryIds);
                });
            })
    
            // Apply sub-category filter
            ->when(!empty($this->subCategoryIds), function ($query) {
                $query->whereHas('subCategories', function ($query) {
                    $query->whereIn('sub_categories.id', $this->subCategoryIds);
                });
            })
    
            // Apply color filter
            ->when(!empty($this->selectedColors), function ($query) {
                $query->whereHas('variation.colors', function ($query) {
                    $query->whereIn('variation_colors.id', $this->selectedColors);
                });
            })
    
            // Apply size filter
            ->when(!empty($this->sizeIds), function ($query) {
                $query->whereHas('variation.sizes', function ($query) {
                    $query->whereIn('variation_sizes.id', $this->sizeIds);
                });
            })
    
            // Apply material filter
            ->when(!empty($this->materialIds), function ($query) {
                $query->whereHas('variation.materials', function ($query) {
                    $query->whereIn('variation_materials.id', $this->materialIds);
                });
            })
    
            // Apply capacity filter
            ->when(!empty($this->capacityIds), function ($query) {
                $query->whereHas('variation.capacities', function ($query) {
                    $query->whereIn('variation_capacities.id', $this->capacityIds);
                });
            })
    
            // Apply price filters
            ->when($this->minPrice, function ($query) {
                $query->whereHas('variation', function ($query) {
                    $query->where('product_variations.price', '>=', $this->minPrice);
                });
            })
            ->when($this->maxPrice, function ($query) {
                $query->whereHas('variation', function ($query) {
                    $query->where('product_variations.price', '<=', $this->maxPrice);
                });
            })
    
            ->with(['variation.images' => function ($query) {
                $query->where(function ($query) {
                    $query->where('priority', 0)
                          ->orWhere('is_primary', 1);
                });
            }])

            // Apply sorting and pagination
            ->orderBy($this->sortBy)
            ->paginate($this->items)->withQueryString();
        
        // Return view with data
        return view('super-admins.pages.tproducts.product-table', [
            'tableData' => $productsQuery,
        ]);
    }
}