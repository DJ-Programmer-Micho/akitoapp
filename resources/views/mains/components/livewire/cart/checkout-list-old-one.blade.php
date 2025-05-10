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
        </style>
        {{-- @php
            print(round($totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100)));
            print('-');
            print($totalListPrice / $exchangeRate) + (($totalListPrice / $exchangeRate) / 100);
            print('-');
            print(round($totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100)));
            print('-');
            print(round($totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100)));
            
        @endphp --}}
        <form action="{{ route('business.checkoutExistingOrder', [
            'locale' => app()->getLocale(), 
            'digit' => $digitPaymentStatus ?? 'none', 
            'orderId' => $orderId,
            'grandTotalUpdated' => ($totalListPrice / $exchangeRate) + (($totalListPrice / $exchangeRate) / 100),
            // 'grandTotalUpdated' => $totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100),
        ])}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-9">
                    <h2 class="checkout-title">{{ __('Select Address:') }} </h2>
                    {{-- {{$digitPaymentStatus}} --}}
                    <div class="row px-3">
                        @if ($addressList)
                        @foreach ($addressList as $index => $address)
                        <div class="col-lg-6 p-0">
                            <div class="card card-dashboard m-1">
                                <div 
                                    class="card address-card {{ $addressSelected == $address->id ? 'selected' : '' }}" 
                                    wire:click="selectAddress({{ $address->id }})"
                                    style="cursor: pointer; {{ $addressSelected == $address->id ? 'border: 2px solid green; background-color: #03810311' : '' }}"
                                    >
                                    <div class="card-body">
                                        <h5 class="card-title">{{ $address->type }}</h5>
                                        <p class="card-text">{{ $address->building_name }}, {{ $address->address }}</p>
                                        <p class="card-text">{{ $address->phone_number }}</p>
                                        <input type="radio" name="address" value="{{ $address->id }}" wire:model="addressSelected" class="d-none">
                                    </div>
                                </div>
                            </div><!-- End .card-dashboard -->
                        </div><!-- End .col-lg-6 -->
                        @endforeach
                        @if (count($addressList) < 5)
                        <div class="col-lg-6 p-0">
                            <a href="{{ route('customer.address', ['locale' => app()->getLocale()]) }}" >
                            <div class="card" style="height: 100%;">
                                <div class="card-body address-card" style="border: 2px dashed black;">
                                        <i class="fa-solid fa-plus" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif
                        @else
                        <div class="col-lg-6 p-0">
                            <a href="{{ route('customer.address', ['locale' => app()->getLocale()]) }}" >
                            <div class="card" style="height: 100%;">
                                <div class="card-body address-card" style="border: 2px dashed black;">
                                        <i class="fa-solid fa-plus" style="position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%);"></i>
                                    </div>
                                </div>
                            </a>
                        </div>
                        @endif

                    </div>

                    <h2 class="checkout-title">{{ __('Select Payment Method:') }}</h2>

                    <div class="row px-3">
                        @foreach ($paymentList as $payment)
                            <div class="col-lg-6 p-0">
                                <div class="card card-dashboard m-1">
                                    <div 
                                        class="card address-card {{ $paymentSelected == $payment->id ? 'selected' : '' }}" 
                                        wire:click="selectPayment({{ $payment->id }})"
                                        style="cursor: pointer; {{ $paymentSelected == $payment->id ? 'border: 2px solid green; background-color: #03810311' : '' }} 
                                        @if($payment->online == 1)
                                            @if($digitPaymentStatus == 0)
                                                opacity: 0.5; pointer-events: none;
                                            @endif
                                        @endif
                                        @if($payment->online == 0)
                                            @if($manualePaymentStatus == 0)
                                                opacity: 0.5; pointer-events: none;
                                            @endif
                                        @endif
                                    ">
                                        <div class="card-body">
                                            <h5 class="card-title">{{ $payment->name }}</h5>
                                            <p class="card-text"><b>Fees: {{ $payment->transaction_fee }}%</b></p>
                                            <input type="radio" value="{{ $payment->id }}" wire:model="paymentSelected" class="d-none">
                                            <input type="hidden" name="payment" value="{{ $paymentSelected }}">
                                        </div>
                                        @if($payment->addon_identifier)
                                            <img src="{{ app('cloudfront') . $payment->addon_identifier }}" 
                                                class="position-absolute" 
                                                style="right: 10px; top: 50%; transform: translateY(-50%); width: 70px; height: auto;"
                                                alt="Payment Logo">
                                        @endif
                                    </div>
                                </div>
                            </div><!-- End .col-lg-6 -->
                        @endforeach
                    </div>

                </div>
                
                <aside class="col-lg-3" dir="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'rtl' : 'ltr' }}">
                    <div class="summary">
                        <h3 class="summary-title">{{ __('Your Order:') }} #{{$order->tracking_number}}</h3><!-- End .summary-title -->

                        <table class="table table-summary" dir="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'rtl' : 'ltr' }}">
                            <thead>
                                <tr>
                                    <th class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-right' : 'text-left' }}">{{ __('Product') }}</th>
                                    <th class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }}">{{ __('QTY X Item') }}</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($orderItems as $item)
                                <tr>
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-right' : 'text-left' }}">
                                        <a href="#" class="d-flex">
                                            <img src="{{ app('cloudfront').$item['product']['variation']['images'][0]['image_path'] }}" 
                                                alt="{{ $item['product']['productTranslation'][0]['name'] }}" 
                                                class="img-fluid" 
                                                style="max-width: 40px; max-height: 40px; object-fit: cover;">
                                            {{ $item['product']['productTranslation'][0]['name'] ?? 'Unknown' }}
                                        </a>
                                    </td>
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }}">
                                        <span class="price flip-symbol">
                                            <span class="cart-product-qty amount">{{ $item['quantity'] }} x</span>
                                            {{ 
                                            number_format(
                                                (
                                                    $item['product']['customer_discount_price'] ?? 
                                                    ($item['product']['discount_price'] ?? $item['product']['base_price'])
                                                ), 
                                                0
                                                ) 
                                            }}
                                            <span class="currency">{{ __('IQD') }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="2">{{__('No Items Found in this Order')}}</td></tr>
                            @endforelse
                                <tr class="summary-subtotal">
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-right' : 'text-left' }}">{{ __('Subtotal:') }}</td>
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }}">
                                        <span class="cart-total-price flip-symbol text-left">
                                            <span class="amount">{{ number_format($totalListPrice, 0) }}</span>
                                            <span class="currency">{{ __('IQD') }}</span>
                                        </span>
                                    </td>
                                </tr>
                                <tr class="summary-total-f">
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-right' : 'text-left' }}">{{ __('Payment Fees:') }}</td>
                                    @if ($transactionFee > 0)
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }}">{{ number_format($transactionFee) }}%</td>
                                    @else
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }} text-success"><b>{{ __('No Fees') }}</b></td>
                                    @endif
                                </tr><!-- End .summary-total -->
                                <tr class="summary-total-f">
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-right' : 'text-left' }}">{{ __('Shipping:') }}</td>
                                    <input type="hidden" name="shipping_amount" value="{{$deliveryCharge}}">
                                    @if ($deliveryCharge > 0)
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }}">
                                        <span class="cart-total-price flip-symbol text-left">
                                            <span class="amount">{{ number_format($deliveryCharge, 0)}} </span>
                                            <span class="currency">{{ __('IQD') }}</span>
                                        </span>
                                    </td>
                                    @else
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }} text-danger"><b>{{ __('FREE') }}</b></td>
                                    @endif
                                </tr><!-- End .summary-total -->
                                <tr class="summary-total-f">
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-right' : 'text-left' }}">{{ __('Grand Total:') }}</td>
                                    <input type="hidden" name="total_amount" value="{{$totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100)}}">
                                    <td class="{{ in_array(app()->getLocale(), ['ar','ku']) ? 'text-left' : 'text-right' }}">
                                        <b>
                                            <span class="cart-total-price flip-symbol text-left">
                                                <span class="amount">{{ number_format($totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100), 0)}} </span>
                                                <span class="currency">{{ __('IQD') }}</span>
                                            </span>
                                        </b>
                                    </td>
                                </tr><!-- End .summary-total -->
                            </tbody>
                        </table>

                        @if (auth('customer')->user()->company_verify == 1)
                            @if ($inZone)
                                <button type="submit" class="btn btn-outline-primary-2 btn-order btn-block" id="order-btn" style="display: none;">
                                    <span class="btn-text">{{ __('Place Order') }}</span>
                                    <span class="btn-hover-text">{{ __('Proceed to Checkout') }}</span>
                                </button>
                                <div id="loading" class="text-center mb-1">
                                    <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                                </div>
                            @else
                                <div class="text-center mt-1">
                                    <h6 class="text-danger">{{ __('Please Add Address First') }}</h6>
                                </div>
                            @endif
                        @else
                            <div class="text-center mt-1">
                                <h6 class="text-danger">{{ __('Please Get Verified First') }}</h6>
                            </div>
                        @endif

                        <script>
                            document.addEventListener("DOMContentLoaded", function() {
                                // Initial page load: Hide loader and show button
                                toggleCheckoutButton();
                        
                                // Listen for Livewire updates
                                Livewire.hook('message.processed', (message, component) => {
                                    toggleCheckoutButton();
                                });
                        
                                function toggleCheckoutButton() {
                                    let orderBtn = document.getElementById("order-btn");
                                    let loadingSpinner = document.getElementById("loading");
                        
                                    if (orderBtn && loadingSpinner) {
                                        orderBtn.style.display = "block";
                                        loadingSpinner.style.display = "none";
                                    }
                                }
                            });
                        </script>                   
                    </div><!-- End .summary -->
                </aside><!-- End .col-lg-3 -->
            </div><!-- End .row -->
        </form>
    </div><!-- End .container -->
</div><!-- End .checkout -->
