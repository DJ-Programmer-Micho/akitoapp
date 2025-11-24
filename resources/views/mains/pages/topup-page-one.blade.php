{{-- resources/views/mains/wallet/topup-form.blade.php --}}
@extends('mains.layout.app')

@section('business-content')
<div class="main">
    <x-mains.components.account.image-header-one />
    <x-mains.components.account.nav-one />

    <div class="checkout">
        <div class="container">
            <style>
                .address-card:hover {
                    background-color: #f8f9fa; /* Light background */
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2); /* Subtle shadow */
                }
                .card-dashboard .card-body {
                    padding: 2rem 2.8rem 2rem;
                }
                .payment-card.selected {
                    border: 2px solid green;
                    background-color: #03810311;
                }
            </style>

            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            
            <form action="{{ route('wallet.topup.start', ['locale' => app()->getLocale()]) }}"
                    method="POST"
                    id="wallet-topup-form">
                @csrf

            <div class="row">
                {{-- LEFT: Top-up form (like checkout left column) --}}
                <div class="col-lg-9">
                    <h2 class="checkout-title">{{ __('Top-up Details') }}</h2>


                        {{-- Amount input --}}
                        <div class="form-group mb-3">
                            <label for="topup-amount" class="form-label">
                                {{ __('Top-up Amount (IQD)') }}
                            </label>
                            <input type="number"
                                   min="10000"
                                   {{-- step="1000" --}}
                                   class="form-control"
                                   id="topup-amount"
                                   name="amount"
                                   value="{{ old('amount', 10000) }}"
                                   required>
                            <small class="form-text text-muted">
                                {{ __('Minimum amount is 10,000 IQD (you can adjust this in validation).') }}
                            </small>
                        </div>

                        {{-- Payment methods (same card style as checkout-list-one) --}}
                        <h2 class="checkout-title">{{ __('Select Payment Method:') }}</h2>
                        <div class="row px-3">
                            @forelse($paymentMethods as $method)
                                @php
                                    $feePercent = (float) ($method->transaction_fee ?? 0);
                                @endphp
                                <div class="col-lg-6 p-0">
                                    <div class="card card-dashboard m-1">
                                        <div class="card address-card payment-card
                                                    {{ old('payment') == $method->id ? 'selected' : '' }}"
                                             style="cursor: pointer;"
                                             onclick="selectTopupPayment('{{ $method->id }}')">

                                            <div class="card-body d-flex align-items-center">
                                                <div class="flex-grow-1">
                                                    <div class="form-check">
                                                        <input class="form-check-input d-none"
                                                               type="radio"
                                                               name="payment"
                                                               id="payment-{{ $method->id }}"
                                                               value="{{ $method->id }}"
                                                               data-fee="{{ $feePercent }}"
                                                               {{ old('payment') == $method->id ? 'checked' : '' }}
                                                               required>
                                                        <h5 class="card-title mb-1">{{ $method->name }}</h5>
                                                    </div>
                                                    <p class="card-text mb-0">
                                                        <b>{{ __('Fees:') }} {{ rtrim(rtrim(number_format($feePercent, 2, '.', ''), '0'), '.') }}%</b>
                                                    </p>
                                                </div>

                                                @if($method->addon_identifier)
                                                    <img src="{{ app('cloudfront') . $method->addon_identifier }}"
                                                         class="position-absolute"
                                                         style="right: 10px; top: 50%; transform: translateY(-50%); width: 70px; height: auto;"
                                                         alt="Payment Logo">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div><!-- End .col-lg-6 -->
                            @empty
                                <div class="col-12">
                                    <div class="alert alert-warning mb-0">
                                        {{ __('No online payment methods are available for top-up.') }}
                                    </div>
                                </div>
                            @endforelse
                        </div>

                        {{-- Summary --}}
                        <div class="card bg-light mb-3 mt-3">
                            <div class="card-body">
                                <h5 class="card-title mb-3">{{ __('Summary') }}</h5>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>{{ __('Top-up Amount') }}</span>
                                    <span>
                                        <span id="summary-amount">0</span>
                                        <span class="small">{{ __('IQD') }}</span>
                                    </span>
                                </div>
                                <div class="d-flex justify-content-between mb-2">
                                    <span>
                                        {{ __('Top-up Fee') }}
                                        (<span id="summary-fee-percent">0</span>%)
                                    </span>
                                    <span>
                                        <span id="summary-fee">0</span>
                                        <span class="small">{{ __('IQD') }}</span>
                                    </span>
                                </div>
                                <hr class="my-2">
                                <div class="d-flex justify-content-between">
                                    <span class="fw-bold">{{ __('Total Charge') }}</span>
                                    <span class="fw-bold">
                                        <span id="summary-total">0</span>
                                        <span class="small">{{ __('IQD') }}</span>
                                    </span>
                                </div>
                            </div>
                        </div>


                    
                </div><!-- /.col-lg-9 -->

                {{-- RIGHT: Wallet summary (like your dashboard cards) --}}
                <aside class="col-lg-3" dir="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'rtl' : 'ltr' }}">
                    <div class="summary">
                        <h3 class="summary-title">{{ __('My Wallet') }}</h3>
                        <table class="table table-summary">
                            <tbody>
                                <tr class="summary-subtotal">
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-right' : 'text-left' }}">
                                        {{ __('Current Balance:') }}
                                    </td>
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }}">
                                        <span class="cart-total-price flip-symbol text-left">
                                            <span class="amount">{{ number_format($walletBalance ?? 0, 0) }}</span>
                                            <span class="currency">{{ __('IQD') }}</span>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="card mt-3">
                            <div class="card-body">
                                <p class="mb-0 text-muted">
                                    {{ __('The wallet balance can be used to pay orders. Top-up payments are processed via the selected online payment method and may include a fee depending on the provider.') }}
                                </p>
                            </div>
                        </div>

                        
                    </div>
                        <button type="submit" class="btn btn-outline-primary-2 btn-order btn-block">
                            <span class="btn-text">{{ __('Proceed to Payment') }}</span>
                            <span class="btn-hover-text">{{ __('Proceed to Payment') }}</span>
                        </button>
                </aside><!-- /.col-lg-3 -->
            </div><!-- /.row -->
            </form>
        </div><!-- /.container -->
    </div><!-- /.checkout -->
</div>
@endsection

@push('scripts')
<script>
    function getSelectedPaymentRadio() {
        return document.querySelector('input[name="payment"]:checked');
    }

    function recalcTopupSummary() {
        const amountInput = document.getElementById('topup-amount');
        if (!amountInput) return;

        const rawAmount = parseInt(amountInput.value || '0', 10);
        const amount = isNaN(rawAmount) ? 0 : rawAmount;

        const selected = getSelectedPaymentRadio();
        let feePercent = 0;

        if (selected && selected.dataset.fee) {
            feePercent = parseFloat(selected.dataset.fee) || 0;
        }

        const fee = Math.round(amount * (feePercent / 100));
        const total = amount + fee;

        document.getElementById('summary-amount').textContent      = amount.toLocaleString();
        document.getElementById('summary-fee').textContent         = fee.toLocaleString();
        document.getElementById('summary-total').textContent       = total.toLocaleString();
        document.getElementById('summary-fee-percent').textContent = feePercent.toString();
    }

    function selectTopupPayment(id) {
        const radio = document.getElementById('payment-' + id);
        if (!radio) return;

        radio.checked = true;

        // highlight selected card like checkout page
        document.querySelectorAll('.payment-card').forEach(card => {
            card.classList.remove('selected');
        });
        const card = radio.closest('.payment-card');
        if (card) {
            card.classList.add('selected');
        }

        recalcTopupSummary();
    }

    document.addEventListener('DOMContentLoaded', function () {
        const amountInput = document.getElementById('topup-amount');
        if (amountInput) {
            amountInput.addEventListener('input', recalcTopupSummary);
        }

        // If one method is pre-checked, mark its card as selected
        const selected = getSelectedPaymentRadio();
        if (selected) {
            const card = selected.closest('.payment-card');
            if (card) card.classList.add('selected');
        } else {
            // auto-select first method if nothing checked
            const firstRadio = document.querySelector('input[name="payment"]');
            if (firstRadio) {
                firstRadio.checked = true;
                const card = firstRadio.closest('.payment-card');
                if (card) card.classList.add('selected');
            }
        }

        recalcTopupSummary();
    });
</script>
@endpush
