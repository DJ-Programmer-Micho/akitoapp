<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use Illuminate\Support\Facades\Storage;
use App\Models\WebSetting;
use App\Models\Category;

class BannerLivewire extends Component
{
    public $base64Images = [];
    public $images = [];
    public $categories = [];
    public $imagesToDelete = [];
    protected $listeners = [
        'imageUploaded' => 'handleImageUploaded',
    ];

    public function mount()
    {
        $settings = WebSetting::find(1);
        if ($settings && $settings->banner_images) {
            $this->images = json_decode($settings->banner_images, true);
        } else {
            $this->images = [['image' => null, 'category_id' => null]]; // Initialize with an empty image entry
        }
        
        $this->categories = Category::with(['categoryTranslation' => function ($query) {
            $query->where('locale', app()->getLocale());
        }])->has('categoryTranslation')->get();
        // Initialize base64 images based on existing images
        $this->base64Images = array_fill(0, count($this->images), null);
    }

    public function handleImageUploaded($index, $base64data)
    {
        $this->base64Images[$index] = $base64data;
    }

    public function addImage()
    {
        $this->base64Images[] = null; // Placeholder for new image
        $this->images[] = ['image' => null, 'category_id' => null]; // Ensure new entry is initialized
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
        ]);

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
                ];
            } elseif (!empty($imageData['image'])) {
                // If an existing image exists, use it
                $storedImages[] = [
                    'image' => $imageData['image'],
                    'category_id' => $imageData['category_id'], // Ensure category ID is saved
                ];
            }
        }

        // Update the database
        WebSetting::updateOrCreate(['id' => 1], [
            'banner_images' => json_encode($storedImages),
        ]);

        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Settings updated successfully!')]);
    }

    private function storeBase64Image($base64data)
    {
        $imageData = explode(',', $base64data);
        $imageType = explode(';', explode(':', $imageData[0])[1])[0];
        $imageExtension = str_replace('image/', '', $imageType);
        $filename = 'web-setting/banner_' . uniqid() . '.' . $imageExtension;

        Storage::disk('s3')->put($filename, base64_decode($imageData[1]), 'public');

        return $filename;
    }

    private function deleteImage($imagePath)
    {
        Storage::disk('s3')->delete($imagePath);
    }

    public function render()
    {
        return view('super-admins.pages.setting.banner.form', [
            'images' => $this->images,
            'categories' => $this->categories,
        ]);
    }
}
