<div>
    <div class="card-body">
        @php
            // Simple formatter: IQD has 0 decimals (minor == main)
            $formatIqd = function (?int $amount) {
                $amount = (int) ($amount ?? 0);
                return number_format($amount, 0) . ' ' . __('IQD');
            };
        @endphp

        {{-- WALLET SUMMARY --}}
        <div class="row mb-4 text-center">
            <div class="col-md-4 col-12 mb-3">
                <div class="card card-dashboard h-100">
                    <div class="card-head">
                        <h5 class="card-title p-2 mb-0">{{ __('Available Balance') }}</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if($wallet)
                            <p class="mb-0">
                                <strong>{{ $formatIqd($wallet->balance_minor) }}</strong>
                            </p>
                        @else
                            <p class="mb-0">
                                <strong>{{ $formatIqd(0) }}</strong>
                            </p>
                        @endif
                        <small class="text-muted d-block p-0 m-0">
                            {{ __('Amounts can be use for purchase.') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-12 mb-3">
                <div class="card card-dashboard h-100">
                    <div class="card-head">
                        <h5 class="card-title p-2 mb-0">{{ __('Locked for Refund') }}</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if($wallet)
                            <p class="mb-0">
                                <strong>{{ $formatIqd($wallet->locked_minor) }}</strong>
                            </p>
                        @else
                            <p class="mb-0">
                                <strong>{{ $formatIqd(0) }}</strong>
                            </p>
                        @endif
                        <small class="text-muted d-block p-0 m-0">
                            {{ __('Amounts reserved for refund review by admin.') }}
                        </small>
                    </div>
                </div>
            </div>

            <div class="col-md-4 col-12 mb-3">
                <div class="card card-dashboard h-100">
                    <div class="card-head">
                        <h5 class="card-title p-2 mb-0">{{ __('(Avail. + Locked)') }}</h5>
                    </div>
                    <div class="card-body p-0 text-center">
                        @if($wallet)
                            <p class="mb-0">
                                <strong>{{ $formatIqd($wallet->balance_minor + $wallet->locked_minor) }}</strong>
                            </p>
                        @else
                            <p class="mb-0">
                                <strong>{{ $formatIqd(0) }}</strong>
                            </p>
                        @endif
                        <small class="text-muted d-block p-0 m-0">
                            {{ __('In Italian Coffee Customer Account.') }}
                        </small>
                    </div>
                </div>
            </div>
        </div>

        {{-- LATER: Top Up BTN --}}
        {{-- You can turn this into a real flow later --}}
        <div class="mb-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0">{{ __('Wallet History') }}</h5>
            <a href="{{ route('wallet.topup.form', ['locale' => app()->getLocale()]) }}" class="btn btn-primary text-white">
                {{ __('Top up') }}
            </a>
        </div>

        {{-- TRANSACTIONS TABLE --}}
        @if ($transactions->count() === 0)
            <div class="py-4 text-center">
                <lord-icon src="https://cdn.lordicon.com/msoeawqm.json"
                           trigger="loop"
                           colors="primary:#405189,secondary:#0ab39c"
                           style="width:72px;height:72px">
                </lord-icon>
                <h5 class="mt-4">{{ __('No wallet movements yet') }}</h5>
                <p class="text-muted mb-0">
                    {{ __('Pay orders online or receive refunds to start using your wallet.') }}
                </p>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-cart table-mobile">
                    <thead>
                        <tr>
                            <th>{{ __('Date') }}</th>
                            <th class="text-center">{{ __('Type') }}</th>
                            <th class="text-center">{{ __('Reason') }}</th>
                            <th class="text-center">{{ __('Amount') }}</th>
                            <th class="text-center">{{ __('Wallet') }}</th>
                            <th class="text-center">{{ __('Order ID') }}</th>
                            <th>{{ __('Note') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($transactions as $tx)
                            @php
                                // Normalize meta (array / json)
                                $meta = is_array($tx->meta)
                                    ? $tx->meta
                                    : (json_decode($tx->meta ?? '[]', true) ?: []);

                                $orderId   = $meta['order_id']  ?? null;
                                $tracking  = $meta['tracking']  ?? null;
                                $note      = $meta['note']      ?? null;
                                $reasonKey = $tx->reason ?? '';

                                // Friendly names for reasons
                                $reasonLabel = match ($reasonKey) {
                                    'wallet_payment'  => __('Order Paid with Wallet'),
                                    'refund_lock'     => __('Refund Pending (Locked)'),
                                    'refund_release'  => __('Refund Approved (to Wallet)'),
                                    'refund_void'     => __('Refund Rejected'),
                                    'manual_topup'    => __('Manual Top-up'),
                                    'payout_to_bank'  => __('Bank Transfer to Customer'),
                                    default           => __($reasonKey ?: 'Wallet Operation'),
                                };


                                // Amount sign & style
                                $signedAmount = $tx->direction === 'credit'
                                    ? '+ ' . $formatIqd($tx->amount_minor)
                                    : '- ' . $formatIqd($tx->amount_minor);

                                $amountClass = $tx->direction === 'credit'
                                    ? 'text-success'
                                    : 'text-danger';
                            @endphp

                            <tr>
                                <td>
                                    {{ $tx->created_at?->format('Y-m-d H:i') }}
                                </td>

                                <td class="text-center">
                                    @if ($tx->direction === 'credit')
                                        <span class="badge bg-success text-white">
                                            {{ __('Credit') }}
                                        </span>
                                    @else
                                        <span class="badge bg-danger text-white">
                                            {{ __('Debit') }}
                                        </span>
                                    @endif
                                </td>

                                <td class="text-center">
                                    {{ $reasonLabel }}
                                </td>

                                <td class="text-center">
                                    <span class="{{ $amountClass }}">
                                        <b>{{ $signedAmount }}</b>
                                    </span>
                                </td>

                                <td class="text-center">
                                    <b>{{ $formatIqd($tx->balance_after_minor) }}</b>
                                </td>

                                <td class="text-center">
                                    @if ($orderId && $tracking)
                                        <a href="{{ route('pdf.order.customer', [
                                                'locale'   => app()->getLocale(),
                                                'tracking' => $tracking,
                                            ]) }}"
                                           target="_blank">
                                            #{{ $tracking }}
                                        </a>
                                    @elseif ($orderId)
                                        #{{ $orderId }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                <td>
                                    @if ($note)
                                        {{ $note }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3 d-flex justify-content-end">
                {{ $transactions->links('pagination::bootstrap-4') }}
            </div>
        @endif
    </div>
</div>
