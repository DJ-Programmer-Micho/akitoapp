<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\Category;
use App\Models\WebSetting;
use App\Models\SubCategory;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class BannerLivewire extends Component
{
    public $base64Images = [];
    public $images = [];
    public $categories = [];
    public $imagesToDelete = [];
    public $subCategories = [];

    protected $listeners = [
        'imageUploaded' => 'handleImageUploaded',
    ];

    public function mount()
    {
        // Fetch the web setting, and if banner images exist, load them
        $settings = WebSetting::find(1);
        if ($settings && $settings->banner_images) {
            $this->images = json_decode($settings->banner_images, true);
        
            // Initialize missing sub_category_id for images if not set
            foreach ($this->images as &$image) {
                if (!array_key_exists('sub_category_id', $image)) {
                    $image['sub_category_id'] = null;
                }
            }
            $this->fetchSubCategoriesForImages();
        } else {
            // Initialize with an empty image entry if none exist
            $this->images = [['image' => null, 'category_id' => null, 'sub_category_id' => null]];
        }
        
        // Load categories with translations
        $this->categories = Category::with(['categoryTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->has('categoryTranslation')->get();
        
        // Initialize base64 images to match the number of images
        $this->base64Images = array_fill(0, count($this->images), null);
    }
    private function fetchSubCategoriesForImages()
    {
        foreach ($this->images as $index => $image) {
            // Fetch subcategories only for images that have a category_id
            if (!empty($image['category_id']) && empty($image['sub_category_id'])) {
                $this->fetchSubCategories($index); // This method will fetch subcategories for this index
            }
        }
    }
    public function fetchSubCategories($index)
    {
        $categoryId = $this->images[$index]['category_id'] ?? null;
    
        // Clear the current subcategory selection if category changes
        if ($categoryId) {
            $this->subCategories[$index] = SubCategory::where('category_id', $categoryId)
                ->with(['subCategoryTranslation' => function ($query) {
                    $query->where('locale', app()->getLocale());
                }])->get()->toArray(); // Convert collection to array
        } else {
            $this->subCategories[$index] = []; // Initialize as empty array instead of collection
        }
    }
    
    
    public function updateSubCategoryId($index, $subCategoryId)
    {
        // Update subcategory ID for the specific image
        $this->images[$index]['sub_category_id'] = $subCategoryId;
    }
    
    public function handleImageUploaded($index, $base64data)
    {
        $this->base64Images[$index] = $base64data;
    }

    public function addImage()
    {
        $this->base64Images[] = null; // Placeholder for new image
        $this->images[] = ['image' => null, 'category_id' => null, 'sub_category_id' => null]; // Ensure new entry is initialized
        $this->subCategories[] = [];
    }

    public function removeImage($index, $isUploaded = false)
    {
        if ($isUploaded) {
            $this->imagesToDelete[] = $this->images[$index]['image'];
            unset($this->images[$index]);
        }
        unset($this->base64Images[$index]);
        $this->base64Images = array_values($this->base64Images);
        $this->images = array_values($this->images);
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Image removed successfully.')]);
    }

    public function saveSettings()
    {
        $this->validate([
            'images.*.category_id' => 'required|integer',
            'images.*.sub_category_id' => 'nullable|integer',
        ]);
        // Delete images marked for deletion from S3
        foreach ($this->imagesToDelete as $image) {
            $this->deleteImage($image);
        }
    
        $storedImages = [];
    
        foreach ($this->images as $index => $imageData) {
            // Save image only if there's a base64 string
            if (isset($this->base64Images[$index]) && $this->base64Images[$index]) {
                $storedImages[] = [
                    'image' => $this->storeBase64Image($this->base64Images[$index]),
                    'category_id' => $imageData['category_id'], // Ensure category ID is saved
                    'sub_category_id' => $imageData['sub_category_id'] ?? null,

                ];
            } elseif (!empty($imageData['image'])) {
                // If an existing image exists, use it
                $storedImages[] = [
                    'image' => $imageData['image'],
                    'category_id' => $imageData['category_id'],
                    'sub_category_id' => $imageData['sub_category_id'] ?? null,
                ];
            }
        }

        // Update the database
        WebSetting::updateOrCreate(['id' => 1], [
            'banner_images' => json_encode($storedImages),
        ]);
    
        // Clear the imagesToDelete array after saving
        $this->imagesToDelete = [];
        
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Settings updated successfully!')]);
    }
    
    private function deleteImage($imagePath)
    {

        try {
            Storage::disk('s3')->delete($imagePath);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'warning', 'message' => __('Weak Internet Or Image Server Disconnected!')]);
        }

    }
    

    private function storeBase64Image($base64data)
    {
        $imageData = explode(',', $base64data);
        $imageType = explode(';', explode(':', $imageData[0])[1])[0];
        $imageExtension = str_replace('image/', '', $imageType);
        $filename = 'web-setting/banners/banner_' . uniqid() . '.' . $imageExtension;

        Storage::disk('s3')->put($filename, base64_decode($imageData[1]), 'public');

        return $filename;
    }

    public function render()
    {
        // dd($this->images,$this->categories,$this->subCategories);
        return view('super-admins.pages.setting.banner.form', [
            'images' => $this->images,
            'categories' => $this->categories,
            'subCategories' => $this->subCategories, // Pass the subcategories to the view
        ]);
    }
}
