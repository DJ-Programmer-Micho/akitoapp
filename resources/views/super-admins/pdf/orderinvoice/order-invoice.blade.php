<div class="page-content">
    <div class="container-fluid">

        <!-- start page title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Invoice Details</h4>

                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="javascript: void(0);">Invoices</a></li>
                            <li class="breadcrumb-item active">Invoice Details</li>
                        </ol>
                    </div>

                </div>
            </div>
        </div>
        <!-- end page title -->

        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-header border-bottom-dashed p-4">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" class="card-logo card-logo-dark" alt="logo dark" width="120">
                                        <img src="{{ app('cloudfront').'web-setting/logo2.png' }}" class="card-logo card-logo-light" alt="logo light" width="120">
                                        <div class="mt-sm-5 mt-4">
                                            <h6 class="text-muted text-uppercase fw-semibold">Address</h6>
                                            <p class="text-muted mb-1" id="address-details">Iraq, Erbil</p>
                                            <p class="text-muted mb-1" id="address-details">100m Empire Pearl, Erbil 44001, Iraq</p>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0 mt-sm-0 mt-3">
                                        {{-- <h6><span class="text-muted fw-normal">Legal Registration No:</span><span id="legal-register-no">{{$orderData->tracking_number}}</span></h6> --}}
                                        <h6><span class="text-muted fw-normal">Email:</span><a href="mailto:support@akitu-co.com" class="link-primary" target="_blank" id="email">support@akitu-co.com</a></h6>
                                        <h6><span class="text-muted fw-normal">Website:</span> <a href="https://akitu-co.com/" class="link-primary" target="_blank" id="website">www.akitu-co.com</a></h6>
                                        <h6 class="mb-0"><span class="text-muted fw-normal">Contact No: </span><a href="tel:009647507747742" class="link-primary" target="_blank" id="contact-no">+964 750 774 7742</a></h6>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Invoice No</p>
                                        <h5 class="fs-14 mb-0">#VL<span id="invoice-no"></span>{{$orderData->tracking_number}}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Date</p>
                                        <h5 class="fs-14 mb-0"><span id="invoice-date">{{$orderData->created_at}}</span> 
                                            {{-- <small class="text-muted" id="invoice-time">02:36PM</small> --}}
                                        </h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Payment Status</p>
                                        <span class="badge bg-success-subtle text-success fs-11" id="payment-status">{{$orderData->payment_status}}</span>
                                    </div>
                                    <!--end col-->
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Total Amount</p>
                                        <h5 class="fs-14 mb-0"><span id="total-amount">{{Number::currency($subTotal + $orderData->shipping_amount)}}</span></h5>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4 border-top border-top-dashed">
                                <div class="row g-3">
                                    <div class="col-6">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Customer Address</h6>
                                        <p class="fw-medium mb-2" id="billing-name">{{$orderData->customer->customer_profile->first_name}} {{$orderData->customer->customer_profile->last_name}}</p>
                                        <p class="text-muted mb-1" id="billing-address-line-1">{{$orderData->customer->customer_profile->country}}, {{$orderData->customer->customer_profile->city}} - {{$orderData->customer->customer_profile->zip_code}}</p>
                                        <p class="text-muted mb-1" id="billing-address-line-1">{{$orderData->customer->customer_profile->address}}</p>
                                        <p class="text-muted mb-1"><span>Phone: </span><span id="billing-phone-no">{{$orderData->customer->customer_profile->phone_number}}</span></p>
                                        {{-- <p class="text-muted mb-0"><span>Tax: </span><span id="billing-tax-no">12-3456789</span> </p> --}}
                                    </div>
                                    <!--end col-->
                                    <div class="col-6">
                                        <h6 class="text-muted text-uppercase fw-semibold mb-3">Shipping Address</h6>
                                        <p class="fw-medium mb-2" id="shipping-name">{{$orderData->first_name}} {{$orderData->last_name}}</p>
                                        <p class="text-muted mb-1" id="shipping-address-line-1">{{$orderData->country}}, {{$orderData->city}} - {{$orderData->zip_code}}</p>
                                        <p class="text-muted mb-1" id="shipping-address-line-1">{{$orderData->address}}</p>
                                        <p class="text-muted mb-1"><span>Phone: </span><span id="shipping-phone-no">{{$orderData->phone_number}}</span></p>
                                    </div>
                                    <!--end col-->
                                </div>
                                <!--end row-->
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="table-responsive">
                                    <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col">Product Details</th>
                                                <th scope="col">Rate</th>
                                                <th scope="col">Quantity</th>
                                                <th scope="col" class="text-end">Amount</th>
                                            </tr>
                                        </thead>
                                        <tbody id="products-list">
                                            @foreach ($orderData->orderItems as $index => $item)
                                            <tr>
                                                <th scope="row">{{$index+1}}</th>
                                                <td class="text-start">
                                                    <span class="fw-medium">{{$item->product_name}}</span>
                                                    {{-- <p class="text-muted mb-0">Graphic Print Men & Women Sweatshirt</p> --}}
                                                </td>
                                                <td>{{Number::currency($item->price)}}</td>
                                                <td>{{$item->quantity}}</td>
                                                <td class="text-end">{{Number::currency($item->total)}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table><!--end table-->
                                </div>
                                <div class="border-top border-top-dashed mt-2">
                                    <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                        <tbody>
                                            <tr>
                                                <td>Sub Total</td>
                                                <td class="text-end">{{Number::currency($subTotal)}}</td>
                                            </tr>
                                            {{-- <tr>
                                                <td>Estimated Tax (12.5%)</td>
                                                <td class="text-end">$44.99</td>
                                            </tr>
                                            <tr>
                                                <td>Discount <small class="text-muted">(VELZON15)</small></td>
                                                <td class="text-end">- $53.99</td>
                                            </tr> --}}
                                            <tr>
                                                <td>Shipping Charge</td>
                                                <td class="text-end">{{Number::currency($orderData->shipping_amount)}}</td>
                                            </tr>
                                            <tr class="border-top border-top-dashed fs-15">
                                                <th scope="row">Total Amount</th>
                                                <th class="text-end">{{Number::currency($subTotal + $orderData->shipping_amount)}}</th>
                                            </tr>
                                        </tbody>
                                    </table>
                                    <!--end table-->
                                </div>
                                <div class="mt-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Signature</h6>
                                    <hr>
                                    {{-- <p class="text-muted mb-1">Payment Method: <span class="fw-medium" id="payment-method">Mastercard</span></p>
                                    <p class="text-muted mb-1">Card Holder: <span class="fw-medium" id="card-holder-name">David Nichols</span></p>
                                    <p class="text-muted mb-1">Card Number: <span class="fw-medium" id="card-number">xxx xxxx xxxx 1234</span></p>
                                    <p class="text-muted">Total Amount: <span class="fw-medium" id="">$ </span><span id="card-total-amount">755.96</span></p> --}}
                                </div>
                                {{-- <div class="mt-3">
                                    <h6 class="text-muted text-uppercase fw-semibold mb-3">Payment Details:</h6>
                                    <p class="text-muted mb-1">Payment Method: <span class="fw-medium" id="payment-method">Mastercard</span></p>
                                    <p class="text-muted mb-1">Card Holder: <span class="fw-medium" id="card-holder-name">David Nichols</span></p>
                                    <p class="text-muted mb-1">Card Number: <span class="fw-medium" id="card-number">xxx xxxx xxxx 1234</span></p>
                                    <p class="text-muted">Total Amount: <span class="fw-medium" id="">$ </span><span id="card-total-amount">755.96</span></p>
                                </div> --}}
                                <div class="mt-4">
                                    <div class="alert alert-info">
                                        <p class="mb-0"><span class="fw-semibold">NOTES:</span>
                                            <span id="note">All accounts are to be paid within 7 days from receipt of invoice. To be paid by cheque or
                                                credit card or direct payment online. If account is not paid within 7
                                                days the credits details supplied as confirmation of work undertaken
                                                will be charged the agreed quoted fee noted above.
                                            </span>
                                        </p>
                                    </div>
                                </div>
                                <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                    <a href="javascript:window.print()" class="btn btn-success"><i class="ri-printer-line align-bottom me-1"></i> Print</a>
                                    <a href="javascript:void(0);" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download</a>
                                </div>
                            </div>
                            <!--end card-body-->
                        </div><!--end col-->
                    </div><!--end row-->
                </div>
                <!--end card-->
            </div>
            <!--end col-->
        </div>
        <!--end row-->

    </div><!-- container-fluid -->
</div><!-- End Page-content -->