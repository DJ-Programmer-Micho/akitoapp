<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Tag;
use App\Models\Brand;
use Livewire\Component;
use App\Models\Category;
use App\Models\VariationCapacity;
use App\Models\VariationColor;
use App\Models\VariationMaterial;
use App\Models\VariationSize;

class CProductLivewire extends Component
{
    // Pre-INT
    public $brands;
    public $categoriesData;
    public $tags;
    public $colors;
    public $sizes;
    public $materials;
    public $capacities;
    // INT
    public $filteredLocales;
    public $glang;
    public $products = [];
    public $contents = [];
    public $selectedCategories = [];
    public $selectedColors = [];
    public $selectedMaterials = [];
    public $selectedSizes = [];
    public $selectedCapacities = [];

    // On Load
    public function mount(){
        $this->glang = app()->getLocale();
        $this->filteredLocales = app('glocales');

        foreach ($this->filteredLocales as $locale) {
            $this->contents[$locale] = ''; // or any default value
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
    public function productSave(){

        dd($this->products, $this->contents);
    }

    public function updatedSelectedCategories($value)
    {
        // Optional: Handle updates or emit events if needed
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
     // Render
     public function render(){ 
        $this->initialLoad();
        return view('super-admins.pages.cproducts.product-form', [
        ]);
    }

}