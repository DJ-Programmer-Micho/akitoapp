<?php

namespace App\Http\Livewire\SuperAdmins\Setting;

use Livewire\Component;
use App\Models\WebSetting;
use Illuminate\Support\Facades\Storage;

class LogoLivewire extends Component
{
    public $tempImgIcon;
    public $tempImgLogo;
    public $objectNameIcon; 
    public $objectNameLogo; 
    public $imgReaderLogo;
    public $imgReaderIcon;

    protected $listeners = [
        'updateCroppedIconImg' => 'handleCroppedImageIcon',
        'updateCroppedLogoImg' => 'handleCroppedImageLogo',
    ];

    public function mount(){
        // Load color values from the database based on the user's ID
        $settings = WebSetting::where('id', '1')->first();
        $appIcon = $settings->app_icon ?? null;
        $appLogo = $settings->logo_image ?? null;

        if($appIcon){
            $this->imgReaderIcon = $appIcon;
            $this->tempImgIcon = app('cloudfront').$appIcon;
        }

        if($appLogo){
            $this->imgReaderLogo = $appLogo;
            $this->tempImgLogo = app('cloudfront').$appLogo;
        }
    }

    public function handleCroppedImageIcon($base64data)
    {
        if ($base64data){

            $settings =  WebSetting::where('id', '1')->first();
            $appIcon = $settings->app_icon;

            if($appIcon){
                $this->imgReaderIcon = $appIcon;
            }

            $microtime = str_replace('.', '', microtime(true));
            $this->objectNameIcon = 'web-setting/icon_' .date('Ydm') . $microtime . '.png';
            $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64data));
            $this->tempImgIcon = $base64data;
            if( $this->imgReaderIcon){
                Storage::disk('s3')->delete($this->imgReaderIcon);
                Storage::disk('s3')->put($this->objectNameIcon, $croppedImage);
                $settings = WebSetting::where('id', '1')->first();
                $settings->app_icon = $this->objectNameIcon;
                $settings->save();
            } else {
                Storage::disk('s3')->put($this->objectNameIcon, $croppedImage);
                $settings = WebSetting::where('id', '1')->first();
                $settings->app_icon = $this->objectNameIcon;
                $settings->save();
            }
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Image Uploaded Successfully')]);
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return 'failed to crop image code...425';
        }
    }

    public function handleCroppedImageLogo($base64data)
    {
        if ($base64data){
            $settings =  WebSetting::where('id', '1')->first();
            $appLogo = $settings->logo_image;

            if($appLogo){
                $this->imgReaderLogo = $appLogo;
            }
            $microtime = str_replace('.', '', microtime(true));
            // $this->objectNameLogo = 'rest/menu/logo_' . auth()->user()->name . '_'.date('Ydm').$microtime.'.jpeg';
            $this->objectNameLogo = 'web-setting/logo_' .date('Ydm') . $microtime . '.png';
            $croppedImage = base64_decode(preg_replace('#^data:image/\w+;base64,#i', '', $base64data));
            $this->tempImgLogo = $base64data;
            if( $this->imgReaderLogo){
                Storage::disk('s3')->delete($this->imgReaderLogo);
                Storage::disk('s3')->put($this->objectNameLogo, $croppedImage);
                $settings = WebSetting::where('id', '1')->first();
                $settings->logo_image = $this->objectNameLogo;
                $settings->save();
            } else {
                Storage::disk('s3')->put($this->objectNameLogo, $croppedImage);
                $settings = WebSetting::firstOrNew(['id' => '1']);
                $settings->logo_image = $this->objectNameLogo;
                $settings->save();
            }
            $this->dispatchBrowserEvent('alert', ['type' => 'success',  'message' => __('Image Uploaded Successfully')]);
        } else {
            $this->dispatchBrowserEvent('alert', ['type' => 'error',  'message' => __('Image did not crop!!!')]);
            return 'failed to crop image code...425';
        }
    }

    public function render(){
        return view('super-admins.pages.setting.logo.form');
    }
}
