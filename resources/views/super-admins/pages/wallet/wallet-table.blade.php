{{-- resources/views/super-admins/pages/wallet/wallet-table.blade.php --}}
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">{{ __('Wallets') }}</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item">
                                <a href="javascript: void(0);">{{ __('Dashboard') }}</a>
                            </li>
                            <li class="breadcrumb-item active">{{ __('Customer Wallets') }}</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-lg-12">
                <div class="card" id="walletList">
                    <div class="card-header border-bottom-dashed">
                        <div class="row g-4 align-items-center">
                            <div class="col-sm">
                                <div>
                                    <h5 class="card-title mb-0">{{ __('Customer Wallet List') }}</h5>
                                </div>
                            </div>
                            {{-- Optional buttons --}}
                        </div>
                    </div>

                    {{-- Filters --}}
                    <div class="card-body border-bottom-dashed border-bottom">
                        <form>
                            <div class="row g-3">
                                <div class="col-xxl-6 col-sm-12">
                                    <div class="search-box">
                                        <input type="text"
                                               class="form-control search"
                                               placeholder="{{ __('Search for customer, email, phone, city, username...') }}"
                                               wire:model.debounce.500ms="searchTerm">
                                        <i class="ri-search-line search-icon"></i>
                                    </div>
                                </div>

                                <div class="col-xxl-2 col-sm-4">
                                    <div>
                                        <input type="date"
                                               class="form-control"
                                               wire:model="startDate"
                                               placeholder="{{ __('Start Date') }}">
                                    </div>
                                </div>

                                <div class="col-xxl-2 col-sm-4">
                                    <div>
                                        <input type="date"
                                               class="form-control"
                                               wire:model="endDate"
                                               placeholder="{{ __('End Date') }}">
                                    </div>
                                </div>

                                <div class="col-xxl-2 col-sm-4">
                                    <div>
                                        <select class="form-control"
                                                data-choices
                                                data-choices-search-false
                                                wire:model="statusFilter">
                                            <option value="">{{ __('Status') }}</option>
                                            <option value="1">{{ __('Active') }}</option>
                                            <option value="0">{{ __('Block') }}</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>

                    {{-- Table --}}
                    <div class="card-body">
                        <div class="table-responsive table-card mb-1">
                            <table class="table align-middle" id="walletTable">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th>{{ __('Customer') }}</th>
                                        <th>{{ __('Email') }}</th>
                                        <th>{{ __('Phone') }}</th>
                                        <th>{{ __('Country - City') }}</th>
                                        <th class="text-center">{{ __('Wallet Balance (IQD)') }}</th>
                                        <th class="text-center">{{ __('Locked (IQD)') }}</th>
                                        <th>{{ __('Joining Date') }}</th>
                                        <th>{{ __('Status') }}</th>
                                        <th class="text-center">{{ __('Action') }}</th>
                                    </tr>
                                </thead>
                                <tbody class="list form-check-all">
                                    @forelse($tableData as $customer)
                                        @php
                                            $profile = $customer->customer_profile;
                                            $wallet  = $customer->wallet;
                                            $balance = $wallet ? (int) $wallet->balance_minor : 0;
                                            $locked  = $wallet ? (int) $wallet->locked_minor  : 0;
                                        @endphp

                                        <tr wire:key="wallet-{{ $customer->id }}">
                                            {{-- Customer --}}
                                            <td class="customer_name align-middle @empty($profile?->first_name) text-danger @endif">
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <div class="d-flex align-items-center">
                                                        <div class="flex-shrink-0 me-2">
                                                            <img src="{{ $profile && $profile->avatar ? app('cloudfront').$profile->avatar : $customerImg }}"
                                                                 alt="{{ $profile->first_name ?? 'Unknown Customer' }}"
                                                                 class="img-fluid rounded-circle"
                                                                 style="width: 60px; height: 60px; object-fit: cover; border-radius: 50%; border: 3px solid white; object-position: center;">
                                                        </div>
                                                        <div>
                                                            <h6 class="mb-0">
                                                                {{ $profile->first_name ?? 'Unknown' }}
                                                                {{ $profile->last_name ?? '' }}
                                                            </h6>
                                                            <p class="mb-0">
                                                                <span>@</span>{{ $customer->username ?? 'Customer' }}
                                                            </p>
                                                        </div>
                                                    </div>
                                                    <div>
                                                        @if ($customer->status == 1)
                                                            <span class="text-success" data-toggle="tooltip" title="{{ __('Active') }}">
                                                                <i class="fa-solid fa-circle"></i>
                                                            </span>
                                                        @else
                                                            <span class="text-danger" data-toggle="tooltip" title="{{ __('Blocked') }}">
                                                                <i class="fa-regular fa-circle"></i>
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>

                                            {{-- Email --}}
                                            <td class="email align-middle">
                                                {{ $customer->email }}
                                            </td>

                                            {{-- Phone --}}
                                            <td class="phone align-middle">
                                                {{ $profile->phone_number ?? '-' }}
                                            </td>

                                            {{-- Country - City --}}
                                            <td class="phone align-middle">
                                                {{ $profile->country ?? '-' }} - {{ $profile->city ?? '-' }}
                                            </td>

                                            {{-- Wallet balance --}}
                                            <td class="align-middle text-center">
                                                <span class="cart-total-price flip-symbol text-left">
                                                    <span class="amount text-success">{{ number_format($balance, 0) }}</span>
                                                    <span class="currency text-success">{{ __('IQD') }}</span>
                                                </span>
                                            </td>

                                            {{-- Locked amount --}}
                                            <td class="align-middle text-center">
                                                <span class="cart-total-price flip-symbol text-left">
                                                    <span class="amount text-warning">{{ number_format($locked, 0) }}</span>
                                                    <span class="currency text-warning">{{ __('IQD') }}</span>
                                                </span>
                                            </td>

                                            {{-- Joining date --}}
                                            <td class="date align-middle">
                                                {{ $customer->created_at }}
                                            </td>

                                            {{-- Status --}}
                                            <td class="status align-middle">
                                                <span class="badge {{ $customer->status ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger' }} text-uppercase">
                                                    {{ $customer->status ? __('Active') : __('BLOCK') }}
                                                </span>
                                            </td>

                                            {{-- Actions --}}
                                            <td class="align-middle text-center">
                                                <span>
                                                    <div class="dropdown">
                                                        <button class="btn btn-soft-secondary btn-sm dropdown"
                                                                type="button"
                                                                data-bs-toggle="dropdown"
                                                                aria-expanded="false">
                                                            <i class="ri-more-fill"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end">
                                                            <li>
                                                                <a type="button"
                                                                   class="dropdown-item edit-list"
                                                                   target="_blank"
                                                                   href="{{ route('super.walletManagementsViewer', ['locale' => app()->getLocale(), 'id' => $customer->id]) }}">
                                                                    <i class="fa-regular fa-eye me-2"></i>{{ __('View Wallet') }}
                                                                </a>
                                                            </li>
                                                            <li>
                                                                <a type="button"
                                                                   class="dropdown-item edit-list"
                                                                   target="_blank"
                                                                   href="{{ route('super.customerProfile', ['locale' => app()->getLocale(), 'id' => $customer->id]) }}">
                                                                    <i class="fa-regular fa-user me-2"></i>{{ __('View Customer') }}
                                                                </a>
                                                            </li>
                                                        </ul>
                                                    </div>
                                                </span>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="9">
                                                <div class="noresult" style="display: block">
                                                    <div class="text-center">
                                                        <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                                                                   trigger="loop"
                                                                   colors="primary:#121331,secondary:#08a88a"
                                                                   style="width:75px;height:75px">
                                                        </lord-icon>
                                                        <h5 class="mt-2">{{ __('Sorry! No Result Found') }}</h5>
                                                        <p class="text-muted mb-0">
                                                            {{ __('We did not find any customer wallets for your search.') }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex justify-content-end">
                            {{ $tableData->links() }}
                        </div>
                    </div>
                </div>
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div>
    <!-- container-fluid -->
</div>
<!-- End Page-content -->
