<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;

class LanguageLivewire extends Component
{
    public $translations = [];

    public function mount()
    {
        // Load JSON files and combine into an array
        $this->loadTranslations();
    }

    public function loadTranslations()
    {
        $en = json_decode(file_get_contents(resource_path('lang/en.json')), true);
        $ar = json_decode(file_get_contents(resource_path('lang/ar.json')), true);
        $ku = json_decode(file_get_contents(resource_path('lang/ku.json')), true);

        foreach ($en as $key => $value) {
            $this->translations[$key] = [
                'en' => $value,
                'ar' => $ar[$key] ?? '', // Fallback to empty string if key not found
                'ku' => $ku[$key] ?? '', // Fallback to empty string if key not found
            ];
        }
    }

    public function saveTranslations()
    {
        // Prepare data for saving
        $en = [];
        $ar = [];
        $ku = [];

        foreach ($this->translations as $key => $translation) {
            $en[$key] = $translation['en'];
            $ar[$key] = $translation['ar'];
            $ku[$key] = $translation['ku'];
        }

        // Save the translations back to JSON files
        file_put_contents(resource_path('lang/en.json'), json_encode($en, JSON_PRETTY_PRINT));
        file_put_contents(resource_path('lang/ar.json'), json_encode($ar, JSON_PRETTY_PRINT));
        file_put_contents(resource_path('lang/ku.json'), json_encode($ku, JSON_PRETTY_PRINT));

        session()->flash('message', 'Translations saved successfully.');
    }

    public function render()
    {
        return view('super-admins.pages.setting.language.table', [
            'translations' => $this->translations,
        ]);
    }
}
