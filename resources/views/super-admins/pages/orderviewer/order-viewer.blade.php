{{-- resources/views/super-admins/pages/orderviewer/order-viewer.blade.php --}}
<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Order Details</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Ecommerce</a></li>
                            <li class="breadcrumb-item active">Order Details</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row">
            <div class="col-xl-9">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <h5 class="card-title flex-grow-1 mb-0">Order #{{$orderData->tracking_number}}</h5>
                            <div class="flex-shrink-0">
                                <a target="_blank" href="{{ route('super.orderInvoice', ['locale' => app()->getLocale(), 'tracking' => $orderData->tracking_number]) }}" class="btn btn-success btn-sm"><i class="fa-regular fa-eye me-1"></i> Print</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive table-card">
                            <table class="table table-nowrap align-middle table-borderless mb-0">
                                <thead class="table-light text-muted">
                                    <tr>
                                        <th scope="col">Product Details</th>
                                        <th scope="col">Item Price (USD)</th>
                                        <th scope="col">Item Price (IQD)</th>
                                        <th scope="col">Quantity</th>
                                        {{-- <th scope="col">Rating</th> --}}
                                        <th scope="col" class="text-end">Total Amount</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($orderData->orderItems as $item)
                                    <tr>
                                        <td>
                                            <div class="d-flex">
                                                <div class="flex-shrink-0 avatar-md bg-light rounded p-1">
                                                    <img src="{{app('cloudfront').$item->product->variation->images[0]->image_path}}" alt="" class="img-fluid d-block bg-white">
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h5 class="fs-14"><a href="apps-ecommerce-product-details.html" class="text-body">{{$item->product_name}}</a></h5>
                                                    {{-- <p class="text-muted mb-0">Color: <span class="fw-medium">Pink</span></p>
                                                    <p class="text-muted mb-0">Size: <span class="fw-medium">M</span></p> --}}
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{Number::currency($item->price_usd)}}</td>
                                        <td>
                                            <span class="cart-total-price flip-symbol text-left">
                                                <span class="amount">{{ number_format($item->price_iqd, 0)}} </span>
                                                <span class="currency">{{ __('IQD') }}</span>
                                            </span>
                                        </td>
                                        <td>{{$item->quantity}}</td>
                                        {{-- <td>
                                            <div class="text-warning fs-15">
                                                <i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-fill"></i><i class="ri-star-half-fill"></i>
                                            </div>
                                        </td> --}}
                                        <td class="fw-medium text-end">
                                            <span class="cart-total-price flip-symbol text-left">
                                                <span class="amount">{{ number_format($item->total_iqd, 0)}} </span>
                                                <span class="currency">{{ __('IQD') }}</span>
                                            </span>
                                        </td>
                                    </tr>
                                    @if ($orderData->payment_status === 'refunded' && (int)($orderData->refunded_minor ?? 0) > 0)
                                        <div class="alert alert-info d-flex align-items-center mt-2" role="alert">
                                            <i class="ri-wallet-3-line me-2"></i>
                                            <div>
                                                {{ __('Refunded :amount IQD to customer wallet.', ['amount' => number_format($orderData->refunded_minor, 0)]) }}
                                            </div>
                                        </div>
                                    @endif

                                    @endforeach
                                    @php
                                        $items = (int) $subTotal;
                                        $shipping = (int) ($orderData->shipping_amount ?? 0);
                                        $total = (int) ($orderData->total_minor ?? ($items + $shipping)); // fallback for older orders
                                        $fee = max(0, $total - $shipping - $items);

                                        $paid = (int) ($orderData->paid_minor ?? 0);
                                        $refunded = (int) ($orderData->refunded_minor ?? 0);
                                        $balance = max(0, $total - $paid);
                                    @endphp
                                    <tr class="border-top border-top-dashed">
                                        <td colspan="3"></td>
                                        <td colspan="2" class="fw-medium p-0">
                                            <table class="table table-borderless mb-0">
                                                <tbody>
                                                    <tr>
                                                        <td>Sub Total :</td>
                                                        <td class="text-end">
                                                            <span class="cart-total-price flip-symbol">
                                                                <span class="amount">{{ number_format($items, 0) }}</span>
                                                                <span class="currency">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Shipping Charge :</td>
                                                        <td class="text-end">
                                                            <span class="cart-total-price flip-symbol">
                                                                <span class="amount text-info">{{ number_format($shipping, 0) }}</span>
                                                                <span class="currency text-info">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Fee Amount:</td>
                                                        <td class="text-end">
                                                            <span class="cart-total-price flip-symbol">
                                                                <span class="amount text-info">{{ number_format($fee, 0) }}</span>
                                                                <span class="currency text-info">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr class="border-top border-top-dashed">
                                                        <th scope="row">Total (IQD) :</th>
                                                        <th class="text-end">
                                                            <span class="cart-total-price flip-symbol">
                                                                <span class="amount text-success">{{ number_format($total, 0) }}</span>
                                                                <span class="currency text-success">{{ __('IQD') }}</span>
                                                            </span>
                                                        </th>
                                                    </tr>

                                                    {{-- NEW: money reconciliation --}}
                                                    <tr class="border-top border-top-dashed">
                                                        <td>Paid:</td>
                                                        <td class="text-end">
                                                            <span class="cart-total-price flip-symbol">
                                                                <span class="amount">{{ number_format($paid, 0) }}</span>
                                                                <span class="currency">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Refunded:</td>
                                                        <td class="text-end">
                                                            <span class="cart-total-price flip-symbol">
                                                                <span class="amount text-danger">{{ number_format($refunded, 0) }}</span>
                                                                <span class="currency text-danger">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td>Balance Due:</td>
                                                        <td class="text-end">
                                                            <span class="cart-total-price flip-symbol">
                                                                <span class="amount">{{ number_format($balance, 0) }}</span>
                                                                <span class="currency">{{ __('IQD') }}</span>
                                                            </span>
                                                        </td>
                                                    </tr>
                                                </tbody>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <!--end card-->
                {{-- <div class="card">
                    <div class="card-header">
                        <div class="d-sm-flex align-items-center">
                            <h5 class="card-title flex-grow-1 mb-0">Order Status</h5>
                            <div class="flex-shrink-0 mt-2 mt-sm-0">
                                <a href="javasccript:void(0;)" class="btn btn-soft-info btn-sm mt-2 mt-sm-0"><i class="ri-map-pin-line align-middle me-1"></i> Change Address</a>
                                <a href="javasccript:void(0;)" class="btn btn-soft-danger btn-sm mt-2 mt-sm-0"><i class="mdi mdi-archive-remove-outline align-middle me-1"></i> Cancel Order</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="profile-timeline">
                            <div class="accordion accordion-flush" id="accordionFlushExample">
                                <div class="accordion-item border-0">
                                    <div class="accordion-header" id="headingOne">
                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 avatar-xs">
                                                    <div class="avatar-title bg-success rounded-circle">
                                                        <i class="ri-shopping-bag-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="fs-14 mb-0">Order Placed - <span class="fw-normal">Wed, 15 Dec 2021</span></h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                        <div class="accordion-body ms-2 ps-5 pt-0">
                                            <h6 class="mb-1">An order has been placed.</h6>
                                            <p class="text-muted">Wed, 15 Dec 2021 - 05:34PM</p>

                                            <h6 class="mb-1">Seller has processed your order.</h6>
                                            <p class="text-muted mb-0">Thu, 16 Dec 2021 - 5:48AM</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border-0">
                                    <div class="accordion-header" id="headingTwo">
                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 avatar-xs">
                                                    <div class="avatar-title bg-success rounded-circle">
                                                        <i class="mdi mdi-gift-outline"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="fs-14 mb-1">Packed - <span class="fw-normal">Thu, 16 Dec 2021</span></h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseTwo" class="accordion-collapse collapse show" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                        <div class="accordion-body ms-2 ps-5 pt-0">
                                            <h6 class="mb-1">Your Item has been picked up by courier partner</h6>
                                            <p class="text-muted mb-0">Fri, 17 Dec 2021 - 9:45AM</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border-0">
                                    <div class="accordion-header" id="headingThree">
                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 avatar-xs">
                                                    <div class="avatar-title bg-success rounded-circle">
                                                        <i class="ri-truck-line"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="fs-14 mb-1">Shipping - <span class="fw-normal">Thu, 16 Dec 2021</span></h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div id="collapseThree" class="accordion-collapse collapse show" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                        <div class="accordion-body ms-2 ps-5 pt-0">
                                            <h6 class="fs-14">RQK Logistics - MFDS1400457854</h6>
                                            <h6 class="mb-1">Your item has been shipped.</h6>
                                            <p class="text-muted mb-0">Sat, 18 Dec 2021 - 4.54PM</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="accordion-item border-0">
                                    <div class="accordion-header" id="headingFour">
                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFour" aria-expanded="false">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 avatar-xs">
                                                    <div class="avatar-title bg-light text-success rounded-circle">
                                                        <i class="ri-takeaway-fill"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="fs-14 mb-0">Out For Delivery</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                                <div class="accordion-item border-0">
                                    <div class="accordion-header" id="headingFive">
                                        <a class="accordion-button p-2 shadow-none" data-bs-toggle="collapse" href="#collapseFile" aria-expanded="false">
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 avatar-xs">
                                                    <div class="avatar-title bg-light text-success rounded-circle">
                                                        <i class="mdi mdi-package-variant"></i>
                                                    </div>
                                                </div>
                                                <div class="flex-grow-1 ms-3">
                                                    <h6 class="fs-14 mb-0">Delivered</h6>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                </div>
                            </div>
                            <!--end accordion-->
                        </div>
                    </div>
                </div> --}}
                <!--end card-->
            </div>
            <!--end col-->
            <div class="col-xl-3">
                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <h5 class="card-title flex-grow-1 mb-0"><i class="mdi mdi-truck-fast-outline align-middle me-1 text-muted"></i> Logistics Details</h5>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" class="badge bg-primary-subtle text-primary fs-11">Track Order</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center">
                            <script src="https://cdn.lordicon.com/lordicon.js"></script>
                            @if ($orderData->driver)
                            <lord-icon src="https://cdn.lordicon.com/uetqnvvg.json" trigger="loop" colors="primary:#405189,secondary:#0ab39c" style="width:80px;height:80px"></lord-icon>
                            @else
                            <lord-icon src="https://cdn.lordicon.com/lltgvngb.json" trigger="loop" delay="2000" state="hover-oscillate" colors="primary:#66d7ee,secondary:#e83a30" style="width:80px;height:80px"></lord-icon>
                            @endif
                            <h5 class="fs-16 mt-2">Akitu Logistics</h5>
                            <p class="text-muted mb-0">CAR MODEL: {{$carModel}}</p>
                            <p class="text-muted mb-0">PLATE NUMBER: {{$plateNumber}}</p>
                            <p class="text-muted mb-0">Payment Mode: {{ $orderData->payment_method }}</p>
                            @if (hasRole([1, 4]))
                            <label class="fs-16 mt-2">Select Driver</label>                        
                            <select class="js-basic-single form-select" name="selectedDriver" wire:model="selectedDriver" wire:change="driverData">
                                <option value="" disabled selected>Select a Driver</option> <!-- Placeholder -->
                                @foreach($driverList as $group)
                                    <optgroup label="{{ $group['text'] }}">
                                        @foreach($group['children'] as $driver)
                                            <option value="{{ $driver['id'] }}">{{ $driver['driverName'] }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                            </select>
                            @endif
                        </div>
                    </div>
                </div>
                <!--end card-->

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <h5 class="card-title flex-grow-1 mb-0"><i class="fa-solid fa-dollar-sign me-1"></i> Payment Status</h5>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="text-center mb-3">
                        @if ($orderData->payment_status == "pending")
                            <span class="badge bg-warning-subtle text-warning text-uppercase" style="font-size: 2em">{{$orderData->payment_status}}</span>
                        @elseif ($orderData->payment_status == "successful")
                            <span class="badge bg-success-subtle text-success text-uppercase" style="font-size: 2em">{{$orderData->payment_status}}</span>
                        @elseif ($orderData->payment_status == "refunded")
                            <span class="badge bg-info-subtle text-info text-uppercase" style="font-size: 2em">{{$orderData->payment_status}}</span>
                        @else
                            <span class="badge bg-danger-subtle text-danger text-uppercase" style="font-size: 2em">{{$orderData->payment_status}}</span>
                        @endif
                    </div>
                        @if (hasRole([1, 3, 5]))
                        <div class="text-center">
                            <select class="form-control" data-choices data-choices-search-false
                                    wire:model="statusPaymentFilter"
                                    wire:change="updatePaymentStatus({{$orderData->id}})">
                                <option value="">{{ __('Status') }}</option>
                                <option value="pending">{{ __('Pending') }}</option>
                                <option value="successful">{{ __('Successful') }}</option>
                                <option value="failed">{{ __('Failed') }}</option>
                                <option value="refunded">{{ __('Refunded') }}</option> {{-- NEW --}}
                            </select>
                            <small class="text-muted d-block mt-1">
                                {{ __('Refund excludes gateway fees. Items + shipping only.') }}
                            </small>
                        </div>
                        @endif
                    </div>
                </div>
                <!--end card-->

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <h5 class="card-title flex-grow-1 mb-0"><i class="mdi mdi-truck-fast-outline align-middle me-1 text-muted"></i> Shipping Status</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if ($orderData->status == "pending")
                            <span class="badge bg-warning-subtle text-warning text-uppercase" style="font-size: 2em">{{$orderData->status}}</span>
                            @elseif ($orderData->status == "shipping")
                            <span class="badge bg-primary-subtle text-primary text-uppercase" style="font-size: 2em">{{$orderData->status}}</span>
                            @elseif ($orderData->status == "delivered")
                            <span class="badge bg-success-subtle text-success text-uppercase" style="font-size: 2em">{{$orderData->status}}</span>
                            @elseif ($orderData->status == "cancelled")
                            <span class="badge bg-danger-subtle text-danger text-uppercase" style="font-size: 2em">{{$orderData->status}}</span>
                            @else
                            <span class="badge bg-secondary-subtle text-secondary text-uppercase" style="font-size: 2em">{{$orderData->status}}</span>
                            @endif
                        </div>
                        @if (hasRole([1, 4, 8]))
                        <div class="text-center">
                            <select class="form-control" data-choices data-choices-search-false wire:model="statusFilter" wire:change="updateStatus({{$orderData->id}})">
                                <option value="">Status</option>
                                <option value="pending">Pending</option>
                                <option value="shipping">Shipping</option>
                                <option value="delivered">Delivered</option>
                                <option value="cancelled">Cancelled</option>
                                <option value="refunded">Refunded</option>
                            </select>
                        </div>
                        @endif
                    </div>
                </div>
                <!--end card-->

                <div class="card">
                    <div class="card-header">
                        <div class="d-flex">
                            <h5 class="card-title flex-grow-1 mb-0">Customer Details</h5>
                            <div class="flex-shrink-0">
                                <a href="javascript:void(0);" class="link-secondary">View Profile</a>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0 vstack gap-3">
                            <li>
                                <div class="d-flex align-items-center">
                                    <div class="flex-shrink-0">
                                        <img src="{{isset($orderData->customer->customer_profile->avatar) ? app('cloudfront').$orderData->customer->customer_profile->avatar : app('userImg')}}" class="avatar-sm rounded">
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6 class="fs-14 mb-1">{{$orderData->first_name}} {{$orderData->last_name}}</h6>
                                        <p class="text-muted mb-0">Customer</p>
                                    </div>
                                </div>
                            </li>
                            <li><i class="ri-mail-line me-2 align-middle text-muted fs-16"></i>{{$orderData->email}}</li>
                            <li><i class="ri-phone-line me-2 align-middle text-muted fs-16"></i>{{$orderData->phone_number}}</li>
                        </ul>
                    </div>
                </div>
                <!--end card-->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-map-pin-line align-middle me-1 text-muted"></i> Customer Address</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                            <li class="fw-medium fs-14">{{$orderData->customer->customer_profile->first_name}} {{$orderData->customer->customer_profile->last_name}}</li>
                            <li>{{$orderData->customer->customer_profile->phone_number}}</li>
                            <li>{{$orderData->customer->customer_profile->address}}</li>
                            <li>{{$orderData->customer->customer_profile->city}} - {{$orderData->customer->customer_profile->zip_code}}</li>
                            <li>{{$orderData->customer->customer_profile->country}}</li>
                        </ul>
                    </div>
                </div>
                <!--end card-->
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-map-pin-line align-middle me-1 text-muted"></i> Shipping Address</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled vstack gap-2 fs-13 mb-0">
                            <li class="fw-medium fs-14">{{$orderData->first_name}} {{$orderData->last_name}}</li>
                            <li>{{$orderData->phone_number}}</li>
                            <li>{{$orderData->address}}</li>
                            <li>{{$orderData->city}} - {{$orderData->zip_code}}</li>
                            <li>{{$orderData->country}}</li>
                        </ul>
                    </div>
                </div>
                <!--end card-->

                {{-- <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0"><i class="ri-secure-payment-line align-bottom me-1 text-muted"></i> Payment Details</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Transactions:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">#VLZ124561278124</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Payment Method:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">Debit Card</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Card Holder Name:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">Joseph Parker</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center mb-2">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Card Number:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">xxxx xxxx xxxx 2456</h6>
                            </div>
                        </div>
                        <div class="d-flex align-items-center">
                            <div class="flex-shrink-0">
                                <p class="text-muted mb-0">Total Amount:</p>
                            </div>
                            <div class="flex-grow-1 ms-2">
                                <h6 class="mb-0">$415.96</h6>
                            </div>
                        </div>
                    </div>
                </div> --}}
                <!--end card-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div><!-- container-fluid -->
</div><!-- End Page-content -->
@push('cProductScripts')
<script src="https://code.jquery.com/jquery-3.7.1.slim.js" integrity="sha256-UgvvN8vBkgO0luPSUl2s8TIlOSYRoGFAX4jlCIm9Adc=" crossorigin="anonymous"></script>

<link rel="stylesheet" href="{{ asset('dashboard/css/select2.css') }}">
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        // Initialize Select2 with placeholder and allowClear option
        $('.js-basic-single').select2({
            placeholder: 'Select a Driver',
            allowClear: true // Allows clearing the selection
        });

        // Update Livewire when Select2 selection changes
        $('.js-basic-single').on('change', function (e) {
            var selectedDriver = $(this).val();
            @this.set('selectedDriver', selectedDriver); // This updates the Livewire property
            @this.driverData(); // Call the driverData function in Livewire
        });
    });
    
    var selectedDriver = @json($selectedDriver);
    $('.js-basic-single').val(selectedDriver).trigger('change');
    console.log(selectedDriver)
    

    // Reinitialize Select2 after Livewire re-renders the component
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', (message, component) => {
            $('.js-basic-single').select2({
                placeholder: 'Select a Driver',
                allowClear: true // Re-apply the allowClear option
            });

            // Re-trigger the placeholder
            if ($('.js-basic-single').val() === '') {
                $('.js-basic-single').select2('val', null); // Clear selection to show placeholder
            }
        });
    });
</script>
@endpush
