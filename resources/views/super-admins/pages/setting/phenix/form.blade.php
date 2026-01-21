<div class="page-content">
    <div class="container-fluid">
        <!-- Page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Phenix Systems') }}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript:void(0);">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ __('Phenix Systems') }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="row">
            <!-- Left: list -->
            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">{{ __('Configured Systems') }}</h5>
                        <button class="btn btn-sm btn-primary" wire:click="createNew">
                            {{ __('Add New') }}
                        </button>
                    </div>

                    <div class="card-body">
                        @if($systems && $systems->count())
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle mb-0">
                                    <thead>
                                    <tr>
                                        <th style="width: 60px;">#</th>
                                        <th>{{ __('Name') }}</th>
                                        <th>{{ __('Code') }}</th>
                                        <th>{{ __('Base URL') }}</th>
                                        <th>{{ __('Active') }}</th>
                                        <th style="width: 180px;">{{ __('Actions') }}</th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    @foreach($systems as $index => $system)
                                        <tr>
                                            <td>{{ $index + 1 }}</td>
                                            <td>{{ $system->name }}</td>
                                            <td><code>{{ $system->code }}</code></td>
                                            <td class="text-break">{{ $system->base_url }}</td>
                                            <td>
                                                @if($system->is_active)
                                                    <span class="badge bg-success">{{ __('Yes') }}</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ __('No') }}</span>
                                                @endif
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button class="btn btn-sm btn-warning"
                                                            wire:click="edit({{ $system->id }})">
                                                        {{ __('Edit') }}
                                                    </button>

                                                    <button class="btn btn-sm btn-danger"
                                                            wire:click="deleteSystem({{ $system->id }})"
                                                            onclick="return confirm('{{ __('Are you sure?') }}')">
                                                        {{ __('Delete') }}
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <p class="mb-0">{{ __('No systems configured yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: form -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">
                            {{ $system_id ? __('Edit System') : __('Create New System') }}
                        </h5>

                        <div class="d-flex gap-2">
                            <button type="button"
                                    class="btn btn-sm btn-outline-secondary"
                                    wire:click="resetForm">
                                {{ __('Clear') }}
                            </button>

                            <button type="button"
                                    class="btn btn-sm btn-outline-success"
                                    wire:click="testConnection"
                                    wire:loading.attr="disabled"
                                    @disabled($testing)>
                                <span wire:loading.remove wire:target="testConnection">{{ __('Test') }}</span>
                                <span wire:loading wire:target="testConnection">{{ __('Testing...') }}</span>
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if($test_result)
                            <div class="alert {{ $test_result['ok'] ? 'alert-success' : 'alert-danger' }}">
                                {{ $test_result['message'] }}
                            </div>
                        @endif

                        <form wire:submit.prevent="saveSystem">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input type="text" class="form-control" wire:model.defer="name"
                                       placeholder="Italian Phenix">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Code (for code usage)') }}</label>
                                <input type="text" class="form-control" wire:model.defer="code"
                                       placeholder="italian, monin, ...">
                                <small class="text-muted">
                                    {{ __('You will use this in code, e.g. $phenix->getItems(\"italian\")') }}
                                </small>
                                @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Base URL') }}</label>
                                <input type="text" class="form-control" wire:model.defer="base_url"
                                       placeholder="http://192.168.100.50:8282">
                                @error('base_url') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Username') }}</label>
                                <input type="text" class="form-control" wire:model.defer="username"
                                       placeholder="username">
                                @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Password') }}</label>
                                <input type="password" class="form-control" wire:model.defer="password"
                                       placeholder="{{ $system_id ? __('Leave blank to keep current') : '******' }}">
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                @if($system_id)
                                    <small class="text-muted">{{ __('Leave blank to keep current password') }}</small>
                                @endif
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Phenix Token') }}</label>
                                <input type="password" class="form-control" wire:model.defer="token"
                                       placeholder="{{ $system_id ? __('Leave blank to keep current') : __('Paste token here') }}">
                                @error('token') <span class="text-danger">{{ $message }}</span> @enderror
                                @if($system_id)
                                    <small class="text-muted">{{ __('Leave blank to keep current token') }}</small>
                                @endif
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="is_active" wire:model.defer="is_active">
                                <label class="form-check-label" for="is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100" wire:loading.attr="disabled">
                                <span wire:loading.remove>{{ __('Save') }}</span>
                                <span wire:loading>{{ __('Saving...') }}</span>
                            </button>
                        </form>

                        <hr>

                        <div class="small text-muted">
                            <div><strong>{{ __('Notes') }}:</strong></div>
                            <ul class="mb-0">
                                <li>{{ __('Phenix API requires Basic Auth + phenixtoken header.') }}</li>
                                <li>{{ __('Prefer private VPN URL like 192.168.100.50:8282.') }}</li>
                                <li>{{ __('Don’t reuse tokens in chats/logs. Rotate if leaked.') }}</li>
                            </ul>
                        </div>

                    </div>
                </div>
            </div>

        </div> <!-- row -->
    </div>
</div>
