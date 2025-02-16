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

        <form action="{{ route('business.checkoutChecker', ['locale' => app()->getLocale(), 'digit' => $digitPaymentStatus ?? 'none', 'nvxf' => auth()->guard('customer')->user()->id])}}" method="POST">
            @csrf
            <div class="row">
                <div class="col-lg-9">
                    <h2 class="checkout-title">Select Address: {{$digitPaymentStatus ?? 'none'}}</h2>

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

                    <h2 class="checkout-title">Select Payment Method:</h2>

                    <div class="row px-3">
                        @foreach ($paymentList as $index => $payment)
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
                                        <input type="radio" value="{{ $payment->id }}" wire:model="paymentSelected" class="d-none" @if($digitPaymentStatus == 0) disabled @endif>
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
                    {{-- <label><h2 class="checkout-title">Order notes (optional)</h2></label>
                    <textarea class="form-control" cols="30" rows="4" placeholder="Notes about your order, e.g. special notes for delivery"></textarea> --}}
                </div>
                <aside class="col-lg-3">
                    <div class="summary">
                        <h3 class="summary-title">Your Order</h3><!-- End .summary-title -->

                        <table class="table table-summary">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>QTY X Item</th>
                                </tr>
                            </thead>

                            <tbody>
                                @forelse($cartListItems as $item)
                                <tr>
                                    <td>
                                        <a href="#" class="d-flex">
                                        <img src="{{ app('cloudfront').$item['product']['variation']['images'][0]['image_path'] }}" alt="{{ $item['product']['product_translation'][0]['name'] }}" class="img-fluid" style="max-width: 40px; max-height: 40px; object-fit: cover;">
                                        {{$item['product']['product_translation'][0]['name'] ?? 'unKnown' }}
                                        </a>
                                    </td>
                                    <td>
                                        {{$item['quantity']}} X ${{ 
                                            number_format(
                                                (
                                                    $item['product']['customer_discount_price'] ?? 
                                                    ($item['product']['discount_price'] ?? $item['product']['base_price'])
                                                ), 
                                                2
                                            ) 
                                        }}
                                        {{-- ${{ (!empty($item['product']['variation']['on_sale']) ? $item['product']['variation']['discount'] : $item['product']['variation']['price']) *  $item['quantity']}} --}}
                                    </td>
                                </tr>
                                @empty
                                {{__('No Items Are Added')}}
                                @endforelse
                                <tr class="summary-subtotal">
                                    <td>Subtotal:</td>
                                    <td>${{ $totalListPrice }}</td>
                                </tr><!-- End .summary-subtotal -->
                                {{-- <tr>
                                    <td colspan="2">
                                        <input type="text" class="form-control" required id="checkout-discount-input" placeholder="Enter your coupon code" wire:model="couponCode">
                                        <button type="submit" class="btn btn-outline-primary w-100">Apply Coupon</button> <!-- Coupon apply button -->
                                    </td>
                                </tr>
                                <tr class="summary-total-f">
                                    <td>Coupen Discount:</td>
                                    <td>${{ $totalListPrice }}</td>
                                </tr><!-- End .summary-total --> --}}
                                <tr class="summary-total-f">
                                    <td>Payment Fees:</td>
                                    @if ($transactionFee > 0)
                                    <td>{{ number_format($transactionFee) }}%</td>
                                    @else
                                    <td class="text-success"><b>No Fees</b></td>
                                    @endif
                                </tr><!-- End .summary-total -->
                                <tr class="summary-total-f">
                                    <td>Shipping:</td>
                                    <input type="hidden" name="shipping_amount" value="{{$deliveryCharge}}">
                                    @if ($deliveryCharge > 0)
                                    <td>${{$deliveryCharge}}</td>
                                    @else
                                    <td class="text-danger"><b>FREE</b></td>
                                    @endif
                                </tr><!-- End .summary-total -->
                                <tr class="summary-total-f">
                                    <td>Grand Total:</td>
                                    <input type="hidden" name="total_amount" value="{{$totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100)}}">
                                    <td><b>${{number_format($totalListPrice + $deliveryCharge + (($totalListPrice) * $transactionFee / 100), 2)}}</b></td>
                                </tr><!-- End .summary-total -->
                            </tbody>
                        </table><!-- End .table table-summary -->

                        @if (auth('customer')->user()->company_verify == 1)
                            @if ($inZone)
                            <!-- Button with a loader -->
                            <button type="submit" class="btn btn-outline-primary-2 btn-order btn-block" id="order-btn" style="display: none;">
                                <span class="btn-text">Place Order</span>
                                <span class="btn-hover-text">Proceed to Checkout</span>
                            </button>
                            <div id="loading" class="text-center mb-1">
                                <i class="fa-solid fa-spinner fa-spin-pulse"></i>
                            </div>
                            @else
                            <div class="text-center mt-1">
                                <h6 class="text-danger">Please Add Address First</h6>
                            </div>
                            @endif
                        @else
                        <div class="text-center mt-1">
                            <h6 class="text-danger">Please Get Verify First</h6>
                        </div>
                        @endif

                        <!-- JavaScript to Show Button After Page Fully Loads -->
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
    @vite('resources/js/app.js')
</div><!-- End .checkout -->