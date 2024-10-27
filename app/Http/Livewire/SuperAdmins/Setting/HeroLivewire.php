<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Storage;

class HeroLivewire extends Component
{
    public $glang;
    public $selectedLanguage = 'en';
    public $base64Images = []; // Temporarily uploaded images per language
    public $uploadedImages = []; // Images already in the database
    public $imagesToDelete = []; // Images marked for deletion in the database

    protected $listeners = ['addHeroImage' => 'addHeroImage'];

    public function mount()
    {
        $this->glang = app('glocales'); // Languages like ['en', 'ar', 'ku']
        $this->selectedLanguage = $this->glang[0]; // Default language selection

        // Initialize empty arrays for each language
        foreach ($this->glang as $lang) {
            $this->base64Images[$lang] = [];
            $this->uploadedImages[$lang] = [];
        }

        // Load images from the database
        $slider = WebSetting::first();
        if ($slider && $slider->hero_images) {
            $this->uploadedImages = json_decode($slider->hero_images, true);
        }
    }

    public function addHeroImage($language, $base64data)
    {
        if ($base64data) {
            // Append the base64 image data for the selected language
            $this->base64Images[$language][] = $base64data;
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Image added successfully for ' . __($language))]);
        }
    }

    public function removeHeroImage($language, $index, $isUploaded = false)
    {
        if ($isUploaded) {
            // Remove from uploaded images and mark for deletion
            $this->imagesToDelete[$language][] = $this->uploadedImages[$language][$index];
            unset($this->uploadedImages[$language][$index]);
            $this->uploadedImages[$language] = array_values($this->uploadedImages[$language]);
        } else {
            // Remove from base64 images
            unset($this->base64Images[$language][$index]);
            $this->base64Images[$language] = array_values($this->base64Images[$language]);
        }
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Image removed successfully for ' . __($language))]);
    }

    public function save()
    {
        $newUploadedImages = $this->uploadedImages; // Preserve existing images from DB
    
        foreach ($this->base64Images as $language => $images) {
            foreach ($images as $base64data) {
                // Decode and save base64 images
                $imageData = explode(',', $base64data);
                $imageType = explode(';', explode(':', $imageData[0])[1])[0];
                $imageExtension = str_replace('image/', '', $imageType);
                $filename = 'web-setting/' . uniqid() . '.' . $imageExtension;
    
                Storage::disk('s3')->put($filename, base64_decode($imageData[1]), 'public');
                $newUploadedImages[$language][] = $filename;
            }
        }
    
        // Delete flagged images from storage
        foreach ($this->imagesToDelete as $language => $images) {
            foreach ($images as $filename) {
                Storage::disk('s3')->delete($filename);
                if (($key = array_search($filename, $newUploadedImages[$language])) !== false) {
                    unset($newUploadedImages[$language][$key]);
                }
            }
        }
    
        // Save updated images to the database
        WebSetting::updateOrCreate(
            ['id' => 1],
            ['hero_images' => json_encode($newUploadedImages)]
        );
    
        // Clear only images marked for deletion
        $this->imagesToDelete = [];
        $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => 'Hero slider updated successfully.']);
    }
    

    public function render()
    {
        return view('super-admins.pages.setting.hero.form', [
            'base64Images' => $this->base64Images,
            'uploadedImages' => $this->uploadedImages,
        ]);
    }
}
