<div class="page-content">

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Re Captcha')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Re Captcha')}}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h5>{{__('Re Captcha Configration')}}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="saveSettings">
                        <div class="form-group">
                            <label for="g_key">Google Recaptcha Key</label>
                            <input type="text" id="g_key" wire:model="g_key" class="form-control mb-3" required>
                            @error('g_key') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>

                        <div class="form-group">
                            <label for="g_secret">Google Recaptcha secret</label>
                            <input type="text" id="g_secret" wire:model="g_secret" class="form-control mb-3" required>
                            @error('g_secret') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <button type="submit" class="btn btn-primary w-100 my-3">Save</button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
