<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice of #VL{{$orderData->tracking_number}}</title>
    <link href="https://akitu-co.com/dashboard/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="https://akitu-co.com/dashboard/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="https://akitu-co.com/dashboard/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <style>
@media print {

    body {
        background-color: transparent
    }
        @page {
            size: A4; /* Set paper size to A4 */
            margin: 20mm; /* Optional: Set page margins */
        }
        
        body, .container-fluid, .page-content {
            width: 100%;
            height: 297mm;
            margin: 0; /* Remove margins for printing */
            padding: 0; /* Remove padding for printing */
            -webkit-print-color-adjust: exact; /* Ensure colors are printed accurately */
        }

        .text-muted{
            font-size: 10pt!important 
        }
        .card {
            page-break-inside: avoid; /* Avoid breaking the card across pages */
        }

        /* Optional: Adjust font sizes for better print readability */
        h5, p, table th, table td {
            font-size: 10pt; /* Adjust font size to fit content on the page */
        }
        
        .table {
            width: 100%; /* Ensure table spans the full width of the page */
        }
    }
    </style>
</head>
<body>
<div class="page-content">
    <div class="container-fluid">

        <div class="row justify-content-center">
            <div class="col-xxl-9">
                <div class="card" id="demo">
                    <div class="row">
                        <div class="col-lg-12">
                            <div class="card-header border-bottom-dashed p-4">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <img src="{{ app('main_logo') }}" class="card-logo card-logo-dark" alt="logo dark" width="120">
                                        <img src="{{ app('main_logo') }}" class="card-logo card-logo-light" alt="logo light" width="120">
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
                                        <div class="py-2">
                                            <div id="qrcode"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!--end card-header-->
                        </div><!--end col-->
                        <div class="col-lg-12">
                            <div class="card-body p-4">
                                <div class="row g-3">
                                    <div class="col-3">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Invoice No</p>
                                        <h5 class="fs-14 mb-0">#VL<span id="invoice-no"></span>{{$orderData->tracking_number}}</h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-3">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Date</p>
                                        <h5 class="fs-14 mb-0"><span id="invoice-date">{{$orderData->created_at}}</span> 
                                            {{-- <small class="text-muted" id="invoice-time">02:36PM</small> --}}
                                        </h5>
                                    </div>
                                    <!--end col-->
                                    <div class="col-3">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Payment Status</p>
                                        @if ($orderData->payment_status == "pending")
                                        <span class="badge bg-warning-subtle text-warning fs-11" id="payment-status">
                                            {{strtoupper($orderData->payment_status)}}
                                        </span>
                                        @elseif ($orderData->payment_status == "successful")
                                        <span class="badge bg-success-subtle text-success fs-11" id="payment-status">
                                            {{strtoupper($orderData->payment_status)}}
                                        </span>
                                        @else
                                        <span class="badge bg-danger-subtle text-danger fs-11" id="payment-status">
                                            {{strtoupper($orderData->payment_status)}}
                                        </span>
                                        @endif
                                    </div>
                                    <!--end col-->
                                    <div class="col-3">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Total Amount</p>
                                        <h5 class="fs-14 mb-0"><span id="total-amount">
                                            <span class="cart-total-price flip-symbol text-left">
                                                <span class="amount">{{ number_format($subTotal + $orderData->shipping_amount + ($orderData->total_amount_iqd - $subTotal), 0)}} </span>
                                                <span class="currency">{{ __('IQD') }}</span>
                                            </span>    
                                        </span></h5>
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
                            <div class="card-body p-4 pt-0">
                                <div class="table-responsive">
                                    <table class="table table-borderless text-center table-nowrap align-middle mb-0">
                                        <thead>
                                            <tr class="table-active">
                                                <th scope="col" style="width: 50px;">#</th>
                                                <th scope="col" class="text-start">Product Details</th>
                                                <th scope="col">Unit Cost</th>
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
                                                <td>
                                                    <span class="cart-total-price flip-symbol text-left">
                                                        <span class="amount">{{ number_format($item->price_iqd, 0)}} </span>
                                                        <span class="currency">{{ __('IQD') }}</span>
                                                    </span>
                                                </td>
                                                <td>{{$item->quantity}}</td>
                                                <td class="text-end">
                                                    <span class="cart-total-price flip-symbol text-left">
                                                        <span class="amount">{{ number_format($item->total_iqd, 0)}} </span>
                                                        <span class="currency">{{ __('IQD') }}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table><!--end table-->
                                </div>
                                <div class="border-top border-top-dashed mt-2">
                                    <table class="table table-borderless table-nowrap align-middle mb-0 ms-auto" style="width:250px">
                                        <tbody>
                                            <tr>
                                                <td>Sub Total :</td>
                                                <td class="text-end">
                                                    <span class="cart-total-price flip-symbol text-left">
                                                        <span class="amount">{{ number_format($subTotal, 0)}} </span>
                                                        <span class="currency">{{ __('IQD') }}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                            {{-- <tr>
                                                <td>Discount <span class="text-muted">(VELZON15)</span> : :</td>
                                                <td class="text-end">-$53.99</td>
                                            </tr> --}}
                                            <tr>
                                                <td>Shipping Charge :</td>
                                                <td class="text-end">
                                                    <span class="cart-total-price flip-symbol text-left">
                                                        <span class="amount text-info">{{ number_format($orderData->shipping_amount, 0)}} </span>
                                                        <span class="currency text-info">{{ __('IQD') }}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Fee Amount:</td>
                                                <td class="text-end">
                                                    <span class="cart-total-price flip-symbol text-left">
                                                        <span class="amount text-info">{{ number_format($orderData->total_amount_iqd - $subTotal, 0)}} </span>
                                                        <span class="currency text-info">{{ __('IQD') }}</span>
                                                    </span>
                                                </td>
                                            </tr>
                                            <tr class="border-top border-top-dashed">
                                                <th scope="row">Total (IQD) :</th>
                                                <th class="text-end">
                                                    <span class="cart-total-price flip-symbol text-left">
                                                        <span class="amount text-success">{{ number_format($subTotal + $orderData->shipping_amount + ($orderData->total_amount_iqd - $subTotal), 0)}} </span>
                                                        <span class="currency text-success">{{ __('IQD') }}</span>
                                                    </span>
                                                </th>
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
                                {{-- <div class="mt-4">
                                    <div class="alert alert-info">
                                        <p class="mb-0"><span class="fw-semibold">NOTES:</span>
                                            <span id="note">All accounts are to be paid within 7 days from receipt of invoice. To be paid by cheque or
                                                credit card or direct payment online. If account is not paid within 7
                                                days the credits details supplied as confirmation of work undertaken
                                                will be charged the agreed quoted fee noted above.
                                            </span>
                                        </p>
                                    </div>
                                </div> --}}
                                <div class="hstack gap-2 justify-content-end d-print-none mt-4">
                                    {{-- <a href="javascript:window.print()" class="btn btn-success"><i class="ri-printer-line align-bottom me-1"></i> Print</a>
                                    <a href="javascript:void(0);" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download</a> --}}
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
<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
    // Latitude and longitude
    const latitude = 33.3152;  // Example: Latitude of Baghdad, Iraq
    const longitude = 44.3661; // Example: Longitude of Baghdad, Iraq
  
    // Google Maps link with the coordinates
    const mapUrl = `https://www.google.com/maps?q=${latitude},${longitude}`;
  
    // Create the QR code
    const qrcode = new QRCode(document.getElementById("qrcode"), {
      text: mapUrl,         // The URL that will be encoded in the QR code
      width: 100,           // QR code width
      height: 100           // QR code height
    });
  </script>
  
</body>
</html>
