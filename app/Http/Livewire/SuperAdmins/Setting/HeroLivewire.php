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
        $this->imagesToDelete[$language] ??= [];
    
        if ($isUploaded) {
            $this->imagesToDelete[$language][] = $this->uploadedImages[$language][$index];
            unset($this->uploadedImages[$language][$index]);
            $this->uploadedImages[$language] = array_values($this->uploadedImages[$language]);
        } else {
            unset($this->base64Images[$language][$index]);
            $this->base64Images[$language] = array_values($this->base64Images[$language]);
        }
    
        $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Image removed successfully for ' . __($language))]);
    }

    public function save()
    {
        $newUploadedImages = $this->uploadedImages;
        
        foreach ($this->base64Images as $language => $images) {
            foreach ($images as $base64data) {
                if (isset($imageData[1])) {
                    $imageData = explode(',', $base64data);
                    $imageType = explode(';', explode(':', $imageData[0])[1])[0];
                    $imageExtension = str_replace('image/', '', $imageType);
                    $filename = 'web-setting/sliders/' . uniqid() . '.' . $imageExtension;

                    Storage::disk('s3')->put($filename, base64_decode($imageData[1]), 'public');
                    $newUploadedImages[$language][] = $filename;
                }
            }
        }

        foreach ($this->imagesToDelete as $language => $images) {
            foreach ($images as $filename) {
                Storage::disk('s3')->delete($filename);
                $newUploadedImages[$language] = array_values(array_filter(
                    $newUploadedImages[$language],
                    fn($file) => $file !== $filename
                ));
            }
        }
        
        WebSetting::updateOrCreate(
            ['id' => 1],
            ['hero_images' => json_encode($newUploadedImages)]
        );

        $this->imagesToDelete = [];
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Hero slider updated successfully.'
        ]);
    }
        

    public function render()
    {
        return view('super-admins.pages.setting.hero.form', [
            'base64Images' => $this->base64Images,
            'uploadedImages' => $this->uploadedImages,
        ]);
    }
}
