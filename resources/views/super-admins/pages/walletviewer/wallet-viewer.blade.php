{{-- resources/views/super-admins/pages/walletviewer/wallet-viewer.blade.php --}}
<div class="page-content">
    <style>
        .filter-colors a {
            position: relative;
            display: block;
            width: 2.4rem;
            height: 2.4rem;
            border-radius: 50%;
            border: .2rem solid #fff;
            margin: 0 .3rem .3rem;
            transition: box-shadow .35s ease;
        }
    </style>

    <div class="container-fluid">
        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">
                        {{ __('Customer Wallet') }} - #{{ $customer->id }}
                    </h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item">
                                <a href="{{ route('super.walletManagements', ['locale' => app()->getLocale()]) }}">
                                    {{ __('Wallets') }}
                                </a>
                            </li>
                            <li class="breadcrumb-item active">
                                {{ __('Wallet Viewer') }}
                            </li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            {{-- Left: Transactions --}}
            <div class="col-xl-9 col-lg-8">
                <div>
                    <div class="card">
                        <div>
                            <div class="card-header">
                                <div class="row align-items-center">
                                    <div class="col">
                                        <ul class="nav nav-tabs-custom card-header-tabs border-bottom-0" role="tablist">
                                            <li class="nav-item">
                                                <a class="nav-link @if($tabFilter === 'all') active @endif"
                                                   style="cursor: pointer"
                                                   wire:click="changeTab('all')"
                                                   role="tab">
                                                    {{ __('All') }}
                                                    <span class="badge bg-danger-subtle text-danger align-middle rounded-pill ms-1">
                                                        {{ $transactions->total() }}
                                                    </span>
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link @if($tabFilter === 'credit') active @endif"
                                                   style="cursor: pointer"
                                                   wire:click="changeTab('credit')"
                                                   role="tab">
                                                    {{ __('Credits') }}
                                                </a>
                                            </li>
                                            <li class="nav-item">
                                                <a class="nav-link @if($tabFilter === 'debit') active @endif"
                                                   style="cursor: pointer"
                                                   wire:click="changeTab('debit')"
                                                   role="tab">
                                                    {{ __('Debits') }}
                                                </a>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            <!-- card body -->
                            <div class="card-body">
                                @if ($transactions->isNotEmpty())
                                    <div class="table-responsive">
                                        <table class="table table-bordered table-striped table-hover table-sm">
                                            <thead>
                                                <tr>
                                                    <th>{{ __('Date') }}</th>
                                                    <th>{{ __('Direction') }}</th>
                                                    <th>{{ __('Amount (IQD)') }}</th>
                                                    <th>{{ __('Balance After (IQD)') }}</th>
                                                    <th>{{ __('Reason') }}</th>
                                                    <th>{{ __('Details') }}</th>
                                                    <th class="text-center">{{ __('Admin Action') }}</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($transactions as $tx)
                                                    @php
                                                        $meta = is_array($tx->meta) ? $tx->meta : (json_decode($tx->meta ?? '[]', true) ?: []);
                                                        $orderId  = $meta['order_id']  ?? null;
                                                        $tracking = $meta['tracking']  ?? null;
                                                        $note     = $meta['note']      ?? null;
                                                    @endphp
                                                    <tr wire:key="tx-{{ $tx->id }}">
                                                        <td class="align-middle">
                                                            {{ $tx->created_at }}
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($tx->direction === 'credit')
                                                                <span class="badge bg-success-subtle text-success">
                                                                    {{ __('Credit') }}
                                                                </span>
                                                            @else
                                                                <span class="badge bg-danger-subtle text-danger">
                                                                    {{ __('Debit') }}
                                                                </span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="cart-total-price flip-symbol text-left">
                                                                <span class="amount">
                                                                    {{ number_format($tx->amount_minor, 0) }}
                                                                </span>
                                                                <span class="currency">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="cart-total-price flip-symbol text-left">
                                                                <span class="amount">
                                                                    {{ number_format($tx->balance_after_minor, 0) }}
                                                                </span>
                                                                <span class="currency">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            <span class="badge bg-dark-subtle text-white">
                                                                {{ $tx->reason ?? '-' }}
                                                            </span>
                                                        </td>
                                                        <td class="align-middle">
                                                            @if ($orderId || $tracking || $note)
                                                                <ul class="mb-0 ps-3">
                                                                    @if ($orderId)
                                                                        <li>
                                                                            {{ __('Order ID') }}: #{{ $orderId }}
                                                                            @if ($tracking)
                                                                                ({{ $tracking }})
                                                                            @endif
                                                                        </li>
                                                                    @endif
                                                                    @if ($note)
                                                                        <li>{{ $note }}</li>
                                                                    @endif
                                                                </ul>
                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            @if ($tx->direction === 'credit' && $tx->reason === 'refund_lock' && hasRole([1, 3, 5]))
                                                                {{-- Approve / Reject locked refund --}}
                                                                <div class="btn-group btn-group-sm" role="group">
                                                                    <button type="button"
                                                                            class="btn btn-success"
                                                                            wire:click="approveLock('{{ $tx->id }}')">
                                                                        <i class="fa-regular fa-circle-check me-1"></i> {{ __('Approve') }}
                                                                    </button>
                                                                    <button type="button"
                                                                            class="btn btn-danger"
                                                                            wire:click="rejectLock('{{ $tx->id }}')">
                                                                        <i class="fa-regular fa-circle-xmark me-1"></i> {{ __('Reject') }}
                                                                    </button>
                                                                </div>

                                                            @elseif ($tx->direction === 'credit'
                                                                && in_array($tx->reason, ['refund_release', 'manual_topup'], true)
                                                                && hasRole([1, 3, 5]))
                                                                {{-- Payout to bank from available wallet balance --}}
                                                                <button type="button"
                                                                        class="btn btn-warning btn-sm"
                                                                        wire:click="payoutToBank('{{ $tx->id }}')">
                                                                    <i class="fa-solid fa-arrow-right-from-bracket me-1"></i>
                                                                    {{ __('Send to Bank') }}
                                                                </button>

                                                            @else
                                                                <span class="text-muted">-</span>
                                                            @endif
                                                        </td>

                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="mt-4">
                                        {{ $transactions->links() }}
                                    </div>
                                @else
                                    <div class="py-4 text-center">
                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                   trigger="loop"
                                                   colors="primary:#405189,secondary:#0ab39c"
                                                   style="width:72px;height:72px">
                                        </lord-icon>
                                        <h5 class="mt-4">{{ __('Sorry! No Result Found') }}</h5>
                                    </div>
                                @endif
                            </div>
                            <!-- end card body -->
                        </div>
                    </div>
                    <!-- end card -->
                </div>
            </div>
            <!-- end col -->

            {{-- Right: Filters & Summary --}}
            <div class="col-xl-3 col-lg-4">
                {{-- Filters --}}
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex mb-3">
                            <div class="flex-grow-1">
                                <h5 class="fs-16">{{ __('Filters') }}</h5>
                            </div>
                            <div class="flex-shrink-0">
                                <a href="#" wire:click.prevent="clearFilters" class="text-decoration-underline">
                                    {{ __('Clear All') }}
                                </a>
                            </div>
                        </div>

                        <div class="mb-3">
                            <input wire:model.debounce.500ms="searchReason"
                                   class="form-control"
                                   type="text"
                                   placeholder="{{ __('Search by reason or note...') }}" />
                        </div>

                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label mb-0">{{ __('From Date') }}</label>
                                <input wire:model="startDate"
                                       class="form-control form-control-sm"
                                       type="date" />
                            </div>
                            <div class="col-6">
                                <label class="form-label mb-0">{{ __('To Date') }}</label>
                                <input wire:model="endDate"
                                       class="form-control form-control-sm"
                                       type="date" />
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label mb-0">{{ __('Min Amount') }}</label>
                                <input wire:model="minAmount"
                                       class="form-control form-control-sm"
                                       type="number"
                                       placeholder="0" />
                            </div>
                            <div class="col-6 mt-2">
                                <label class="form-label mb-0">{{ __('Max Amount') }}</label>
                                <input wire:model="maxAmount"
                                       class="form-control form-control-sm"
                                       type="number"
                                       placeholder="100000" />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Wallet Summary --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa-solid fa-wallet me-1"></i> {{ __('Wallet Summary') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @php
                            $balance = (int) $wallet->balance_minor;
                            $locked  = (int) $wallet->locked_minor;
                        @endphp
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ __('Balance') }}</span>
                                <span class="fw-bold text-success">
                                    {{ number_format($balance, 0) }} {{ __('IQD') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ __('Locked') }}</span>
                                <span class="fw-bold text-warning">
                                    {{ number_format($locked, 0) }} {{ __('IQD') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ __('Total Credits') }}</span>
                                <span class="fw-bold text-primary">
                                    {{ number_format($creditTotal, 0) }} {{ __('IQD') }}
                                </span>
                            </li>
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>{{ __('Total Debits') }}</span>
                                <span class="fw-bold text-danger">
                                    {{ number_format($debitTotal, 0) }} {{ __('IQD') }}
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>

                {{-- Manual Wallet Action (Top-up / Send to Bank) --}}
                @if (hasRole([1, 3, 5])) {{-- only certain roles, adjust if needed --}}
                    <div class="card mt-3">
                        <div class="card-header">
                            <h5 class="card-title mb-0">
                                <i class="fa-solid fa-sliders me-1"></i> {{ __('Manual Wallet Action') }}
                            </h5>
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <label class="form-label mb-1">{{ __('Action Type') }}</label>
                                <select wire:model="manualType" class="form-select form-select-sm">
                                    <option value="payout">{{ __('Send to Bank (Deduct)') }}</option>
                                    <option value="topup">{{ __('Add to Wallet (Top-up)') }}</option>
                                </select>
                            </div>

                            <div class="mb-2">
                                <label class="form-label mb-1">{{ __('Amount (IQD)') }}</label>
                                <input type="number"
                                       class="form-control form-control-sm @error('manualAmount') is-invalid @enderror"
                                       wire:model.defer="manualAmount"
                                       min="1"
                                       placeholder="10000" />
                                @error('manualAmount')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label mb-1">{{ __('Note (optional)') }}</label>
                                <textarea class="form-control form-control-sm @error('manualNote') is-invalid @enderror"
                                          wire:model.defer="manualNote"
                                          rows="2"
                                          placeholder="{{ __('Reason, reference, or ticket number...') }}"></textarea>
                                @error('manualNote')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>

                            <button type="button"
                                    class="btn btn-sm btn-primary w-100"
                                    wire:click="submitManualAction">
                                <i class="fa-solid fa-check me-1"></i>
                                {{ __('Apply Wallet Action') }}
                            </button>

                            <small class="text-muted d-block mt-2">
                                {{ __('Payout will deduct from wallet balance only. Locked amount is not affected.') }}
                            </small>
                        </div>
                    </div>
                @endif

                {{-- Customer Details --}}
                <div class="card mt-3">
                    <div class="card-header">
                        <h5 class="card-title mb-0">
                            <i class="fa-regular fa-user me-1"></i> {{ __('Customer Details') }}
                        </h5>
                    </div>
                    <div class="card-body">
                        @php $profile = $customer->customer_profile; @endphp
                        <ul class="list-unstyled mb-0 vstack gap-3">
                            <li>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img src="{{ $profile && $profile->avatar ? app('cloudfront').$profile->avatar : app('userImg') }}"
                                             class="avatar-sm rounded">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="fs-14 mb-1">
                                            {{ $profile->first_name ?? 'Unknown' }} {{ $profile->last_name ?? '' }}
                                        </h6>
                                        <p class="text-muted mb-0">{{ '@'.$customer->username }}</p>
                                    </div>
                                </div>
                            </li>
                            <li>
                                <i class="ri-mail-line me-2 align-middle text-muted fs-16"></i>
                                {{ $customer->email }}
                            </li>
                            <li>
                                <i class="ri-phone-line me-2 align-middle text-muted fs-16"></i>
                                {{ $profile->phone_number ?? '-' }}
                            </li>
                            <li>
                                <i class="ri-map-pin-line me-2 align-middle text-muted fs-16"></i>
                                {{ $profile->country ?? '-' }} - {{ $profile->city ?? '-' }}
                            </li>
                        </ul>
                    </div>
                </div>

            </div>
            <!-- end col -->
        </div>
        <!-- end row -->

    </div>
</div>
