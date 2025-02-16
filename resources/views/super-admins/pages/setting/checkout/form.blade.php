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
                        <h5>{{ __('Free Delivery Price') }}</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveSettings">
                            <div class="form-group">
                                <label for="free_delivery">Free Delivery Price</label>
                                <input type="number" id="free_delivery" wire:model="free_delivery" class="form-control mb-3">
                                @error('free_delivery') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            
            <div class="col-6">
                <div class="card">
                    <div class="card-header">
                        <h5>{{ __('Dollar Price ($)') }}</h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveSettings">
                            <div class="form-group">
                                <label for="exchange_price">Exchange Price</label>
                                <input type="url" id="exchange_price" wire:model="exchange_price" class="form-control mb-3">
                                <small class="text-info">(e.g. 1320 IQD)</small>
                                @error('exchange_price') <span class="text-danger">{{ $message }}</span> @enderror
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
