{{-- resources/views/super-admins/pages/setting/phenix/systems.blade.php --}}
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
                        @if($systems->count())
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th>#</th>
                                    <th>{{ __('Name') }}</th>
                                    <th>{{ __('Code') }}</th>
                                    <th>{{ __('Base URL') }}</th>
                                    <th>{{ __('Active') }}</th>
                                    <th>{{ __('Actions') }}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($systems as $index => $system)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>{{ $system->name }}</td>
                                        <td>{{ $system->code }}</td>
                                        <td>{{ $system->base_url }}</td>
                                        <td>
                                            @if($system->is_active)
                                                <span class="badge bg-success">{{ __('Yes') }}</span>
                                            @else
                                                <span class="badge bg-secondary">{{ __('No') }}</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-warning"
                                                    wire:click="edit({{ $system->id }})">
                                                {{ __('Edit') }}
                                            </button>
                                            <button class="btn btn-sm btn-danger"
                                                    wire:click="deleteSystem({{ $system->id }})"
                                                    onclick="return confirm('{{ __('Are you sure?') }}')">
                                                {{ __('Delete') }}
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @else
                            <p class="mb-0">{{ __('No systems configured yet.') }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Right: form -->
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">
                            {{ $system_id ? __('Edit System') : __('Create New System') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        <form wire:submit.prevent="saveSystem">
                            <div class="mb-3">
                                <label class="form-label">{{ __('Name') }}</label>
                                <input type="text" class="form-control" wire:model="name"
                                       placeholder="Italian Phenix">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Code (for code usage)') }}</label>
                                <input type="text" class="form-control" wire:model="code"
                                       placeholder="italian, monin, ...">
                                <small class="text-muted">
                                    {{ __('You will use this in code, e.g. $phenix->getItems(\"italian\")') }}
                                </small>
                                @error('code') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Base URL') }}</label>
                                <input type="text" class="form-control" wire:model="base_url"
                                       placeholder="http://192.168.100.50:8282">
                                @error('base_url') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Username') }}</label>
                                <input type="text" class="form-control" wire:model="username"
                                       placeholder="xyz">
                                @error('username') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label">{{ __('Password') }}</label>
                                <input type="password" class="form-control" wire:model="password"
                                       placeholder="******">
                                @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" role="switch"
                                       id="is_active" wire:model="is_active">
                                <label class="form-check-label" for="is_active">
                                    {{ __('Active') }}
                                </label>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">
                                {{ __('Save') }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

        </div> <!-- row -->
    </div>
</div>
