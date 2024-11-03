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

        foreach ($this->glang as $lang) {
            $this->base64Images[$lang] = [];
            $this->uploadedImages[$lang] = [];
        }

        $slider = WebSetting::first();
        if ($slider && $slider->hero_images) {
            $this->uploadedImages = json_decode($slider->hero_images, true);
        }
    }

    public function addHeroImage($language, $base64data)
    {
        if ($base64data) {
            $this->base64Images[$language][] = [
                'data' => $base64data,
                'priority' => 0
            ];
            $this->sortImagesByPriority($this->base64Images[$language]);
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Image added successfully for ') . strtoupper($language)
            ]);
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
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => __('Image removed successfully for ') . strtoupper($language)
        ]);
    }

    public function save()
    {
        $newUploadedImages = $this->uploadedImages;
    
        // Capture priorities from input fields
        foreach ($this->base64Images as $language => $images) {
            foreach ($images as $index => $imageData) {
                // Assuming you store priority in $imageData['priority']
                $newUploadedImages[$language][] = [
                    'filename' => $this->uploadBase64ImageToS3($imageData['data']),
                    'priority' => request()->input("priority_{$language}_{$index}") ?? $imageData['priority']
                ];
            }
        }
    
        // Remove marked images
        $this->deleteMarkedImages();
    
        // Sort images by priority
        foreach ($newUploadedImages as $language => &$images) {
            $this->sortImagesByPriority($images);
        }
    
        // Save to database
        WebSetting::updateOrCreate(
            ['id' => 1],
            ['hero_images' => json_encode($newUploadedImages)]
        );
    
        // Clear delete markers and notify user
        $this->imagesToDelete = [];
        $this->dispatchBrowserEvent('alert', [
            'type' => 'success',
            'message' => 'Hero slider updated successfully.'
        ]);
    }
    

    private function uploadBase64ImageToS3($base64data)
    {
        $decodedData = explode(',', $base64data);
        if (isset($decodedData[1])) {
            $imageType = explode(';', explode(':', $decodedData[0])[1])[0];
            $imageExtension = str_replace('image/', '', $imageType);
            $filename = 'web-setting/sliders/' . uniqid() . '.' . $imageExtension;

            Storage::disk('s3')->put($filename, base64_decode($decodedData[1]), 'public');
            return $filename;
        }
        return null;
    }

    private function deleteMarkedImages()
    {
        foreach ($this->imagesToDelete as $language => $images) {
            foreach ($images as $filename) {
                Storage::disk('s3')->delete($filename);
                $this->uploadedImages[$language] = array_values(array_filter(
                    $this->uploadedImages[$language],
                    fn($file) => $file['filename'] !== $filename
                ));
            }
        }
    }

    private function sortImagesByPriority(&$images)
    {
        usort($images, fn($a, $b) => $a['priority'] <=> $b['priority']);
    }

    public function render()
    {
        return view('super-admins.pages.setting.hero.form', [
            'base64Images' => $this->base64Images,
            'uploadedImages' => $this->uploadedImages,
        ]);
    }
}
