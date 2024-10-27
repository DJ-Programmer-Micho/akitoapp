<div class="page-content">

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{__('Logo/Icon')}}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">{{__('Dashboard')}}</a></li>
                            <li class="breadcrumb-item active">{{__('Logo/Icon')}}</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->
        <div class="container">
            <div class="card">
                <div class="card-header">
                    <h5>{{__('Email Configration')}}</h5>
                </div>
                <div class="card-body">
                    <form wire:submit.prevent="save">
                        <div class="form-group">
                            <label for="email_host">Email Mailer</label>
                            <input type="text" id="email_mailer" wire:model="email_mailer" class="form-control mb-3" required>
                            @error('email_mailer') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="form-group">
                            <label for="email_host">Email Host</label>
                            <input type="text" id="email_host" wire:model="email_host" class="form-control mb-3" required>
                            @error('email_host') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="email_port">Email Port</label>
                            <input type="number" id="email_port" wire:model="email_port" class="form-control mb-3" required>
                            @error('email_port') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="email_username">Email Username</label>
                            <input type="text" id="email_username" wire:model="email_username" class="form-control mb-3" required>
                            @error('email_username') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="email_password">Email Password</label>
                            <input type="password" id="email_password" wire:model="email_password" class="form-control mb-3" required>
                            @error('email_password') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="email_encryption">Encryption (SSL/TLS)</label>
                            <select id="email_encryption" wire:model="email_encryption" class="form-control mb-3">
                                <option value="">None</option>
                                <option value="ssl">SSL</option>
                                <option value="tls">TLS</option>
                            </select>
                            @error('email_encryption') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="email_from_address">From Email Address</label>
                            <input type="email" id="email_from_address" wire:model="email_from_address" class="form-control mb-3" required>
                            @error('email_from_address') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <div class="form-group">
                            <label for="email_from_name">From Name</label>
                            <input type="text" id="email_from_name" wire:model="email_from_name" class="form-control mb-3" required>
                            @error('email_from_name') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                
                        <button type="submit" class="btn btn-primary w-100 my-3">Save</button>
                    </form>
                </div>
            </div>
        </div>
        
    </div>
</div>
