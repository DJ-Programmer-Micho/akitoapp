<div class="page-content">
    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Information') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Information') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->
        
        <div class="row">
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Website Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveSettings">
                            <div class="form-group">
                                <label for="email_address">Email Address</label>
                                <input type="email" id="email_address" wire:model="email_address" class="form-control mb-3">
                                @error('email_address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                            <div class="form-group">
                                <label for="phone_number">Phone Number 1</label>
                                <input type="text" id="phone_number" wire:model="phone_number" class="form-control mb-3">
                                @error('phone_number') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="phone_number_2">Phone Number 2</label>
                                <input type="text" id="phone_number_2" wire:model="phone_number_2" class="form-control mb-3">
                                @error('phone_number_2') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                            <div class="form-group">
                                <label for="address">Address</label>
                                <input type="text" id="address" wire:model="address" class="form-control mb-3">
                                @error('address') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                            <div class="form-group">
                                <label for="working_days">Working Days</label>
                                <input type="text" id="working_days" wire:model="working_days" class="form-control mb-3" placeholder="e.g., Sat - Thu">
                                @error('working_days') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                            <div class="form-group">
                                <label for="working_time">Working Time</label>
                                <input type="text" id="working_time" wire:model="working_time" class="form-control mb-3" placeholder="e.g., 9am - 7pm UTC +3">
                                @error('working_time') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Website SM Information') }}</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveSettings">
                            <div class="form-group">
                                <label for="facebook_url">Facebook URL</label>
                                <input type="url" id="facebook_url" wire:model="facebook_url" class="form-control mb-3">
                                @error('facebook_url') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                            <div class="form-group">
                                <label for="instagram_url">Instagram URL</label>
                                <input type="url" id="instagram_url" wire:model="instagram_url" class="form-control mb-3">
                                @error('instagram_url') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        
                            <div class="form-group">
                                <label for="tiktok_url">TikTok URL</label>
                                <input type="url" id="tiktok_url" wire:model="tiktok_url" class="form-control mb-3">
                                @error('tiktok_url') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-group">
                                <label for="snapchat_url">SnapChat URL</label>
                                <input type="url" id="snapchat_url" wire:model="snapchat_url" class="form-control mb-3">
                                @error('snapchat_url') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-12 text-right">
                <button type="submit" class="btn btn-primary mt-3" wire:click="saveSettings">Save Settings</button>
            </div>
        </div>
    </div>
</div>
