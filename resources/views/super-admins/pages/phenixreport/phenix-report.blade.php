<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Phenix Sync Reports') }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript:void(0)">{{ __('Dashboard') }}</a></li>
                            <li class="breadcrumb-item active">{{ __('Phenix Reports') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        {{-- Filters --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body d-flex gap-2 flex-wrap align-items-end">

                        <div style="min-width:220px">
                            <label class="form-label mb-1">{{ __('System') }}</label>
                            <select class="form-select form-select-sm" wire:model="phenix_system_id">
                                <option value="all">{{ __('All Systems') }}</option>

                                @foreach($phenixSystems as $sys)
                                    <option value="{{ $sys->id }}">{{ $sys->name }} ({{ $sys->code }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div style="min-width:220px">
                            <label class="form-label mb-1">{{ __('Search') }}</label>
                            <input class="form-control form-control-sm"
                                   type="text"
                                   wire:model.debounce.400ms="search"
                                   placeholder="{{ __('system_code / path...') }}">
                        </div>

                        <div>
                            <label class="form-label mb-1">{{ __('From') }}</label>
                            <input class="form-control form-control-sm" type="date" wire:model="dateFrom">
                        </div>

                        <div>
                            <label class="form-label mb-1">{{ __('To') }}</label>
                            <input class="form-control form-control-sm" type="date" wire:model="dateTo">
                        </div>

                        <div style="width:120px">
                            <label class="form-label mb-1">{{ __('Per Page') }}</label>
                            <select class="form-select form-select-sm" wire:model="perPage">
                                <option value="10">10</option>
                                <option value="20">20</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                            </select>
                        </div>

                        <div class="ms-auto">
                            <button class="btn btn-sm btn-secondary" wire:click="clearFilters">
                                {{ __('Clear') }}
                            </button>
                        </div>

                    </div>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">

                        @if($logs->count())
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped table-sm align-middle">
                                    <thead>
                                        <tr>
                                            <th>{{ __('Synced At') }}</th>
                                            <th>{{ __('System') }}</th>
                                            <th class="text-center">{{ __('Matched') }}</th>
                                            <th class="text-center">{{ __('Updated') }}</th>
                                            <th class="text-center">{{ __('Changes') }}</th>
                                            <th>{{ __('XLSX') }}</th>
                                            <th class="text-center">{{ __('Action') }}</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($logs as $log)
                                            <tr>
                                                <td>{{ optional($log->synced_at)->format('Y-m-d H:i:s') ?? '-' }}</td>
                                                <td>
                                                    {{ $log->system?->name ?? '-' }}
                                                    <div class="text-muted small">{{ $log->system_code ?? '' }}</div>
                                                </td>
                                                <td class="text-center">{{ $log->matched }}</td>
                                                <td class="text-center">{{ $log->updated }}</td>
                                                <td class="text-center">{{ $log->changes }}</td>
                                                <td class="small">
                                                    @if($log->xlsx_path)
                                                        <code>{{ $log->xlsx_path }}</code>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="text-center">
                                                    <button class="btn btn-sm btn-success"
                                                            wire:click="downloadXlsx({{ $log->id }})"
                                                            @disabled(!$log->xlsx_path)>
                                                        {{ __('Download') }}
                                                    </button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="mt-3">
                                {{ $logs->links() }}
                            </div>
                        @else
                            <div class="alert alert-info mb-0">
                                {{ __('No sync logs found.') }}
                            </div>
                        @endif

                    </div>
                </div>
            </div>
        </div>

        {{-- Download Modal --}}
        <div wire:ignore.self class="modal fade" id="downloadModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">{{ __('Download XLSX') }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="{{ __('Close') }}"></button>
                    </div>

                    <div class="modal-body">
                        @if($downloadPath)
                            <div class="mb-2">
                                <div class="small text-muted">{{ __('Path') }}</div>
                                <code>{{ $downloadPath }}</code>
                            </div>
                        @endif

                        @if($downloadUrl)
                            <a class="btn btn-success w-100" href="{{ $downloadUrl }}" target="_blank">
                                {{ __('Open Download Link') }}
                            </a>
                            <div class="small text-muted mt-2">
                                {{ __('Link expires after 60 minutes.') }}
                            </div>
                        @else
                            <div class="alert alert-warning mb-0">
                                {{ __('No downloadable URL available.') }}
                            </div>
                        @endif
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                    </div>

                </div>
            </div>
        </div>

    </div>

    @push('super_script')
        <script>
            window.addEventListener('open-download-modal', function () {
                const el = document.getElementById('downloadModal');
                const modal = new bootstrap.Modal(el);
                modal.show();
            });
        </script>
    @endpush
</div>
