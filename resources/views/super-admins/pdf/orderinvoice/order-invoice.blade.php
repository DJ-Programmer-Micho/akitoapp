<div class="page-content">
    <style>
        @media print {
            :root, [data-bs-theme=light] {
                --vz-blue: #6691e7;
                --vz-indigo: #405189;
                --vz-purple: #865ce2;
                --vz-pink: #f672a7;
                --vz-red: #ed5e5e;
                --vz-orange: #f1963b;
                --vz-yellow: #e8bc52;
                --vz-green: #13c56b;
                --vz-teal: #02a8b5;
                --vz-cyan: #50c3e6;
                --vz-white: #fff;
                --vz-gray: #878a99;
                --vz-gray-dark: #363d48;
                --vz-gray-100: #f3f6f9;
                --vz-gray-200: #eff2f7;
                --vz-gray-300: #e9ebec;
                --vz-gray-400: #ced4da;
                --vz-gray-500: #adb5bd;
                --vz-gray-600: #878a99;
                --vz-gray-700: #495057;
                --vz-gray-800: #363d48;
                --vz-gray-900: #212529;
                --vz-primary: #6691e7;
                --vz-secondary: #865ce2;
                --vz-success: #13c56b;
                --vz-info: #50c3e6;
                --vz-warning: #e8bc52;
                --vz-danger: #ed5e5e;
                --vz-light: #f3f6f9;
                --vz-dark: #363d48;
                --vz-primary-rgb: 102, 145, 231;
                --vz-secondary-rgb: 134, 92, 226;
                --vz-success-rgb: 19, 197, 107;
                --vz-info-rgb: 80, 195, 230;
                --vz-warning-rgb: 232, 188, 82;
                --vz-danger-rgb: 237, 94, 94;
                --vz-light-rgb: 243, 246, 249;
                --vz-dark-rgb: 54, 61, 72;
                --vz-primary-text-emphasis: #577bc4;
                --vz-secondary-text-emphasis: #724ec0;
                --vz-success-text-emphasis: #10a75b;
                --vz-info-text-emphasis: #44a6c4;
                --vz-warning-text-emphasis: #c5a046;
                --vz-danger-text-emphasis: #c95050;
                --vz-light-text-emphasis: #ced4da;
                --vz-dark-text-emphasis: #363d48;
                --vz-primary-bg-subtle: #e8effb;
                --vz-secondary-bg-subtle: #ede7fb;
                --vz-success-bg-subtle: #dcf6e9;
                --vz-info-bg-subtle: #e5f6fb;
                --vz-warning-bg-subtle: #fcf5e5;
                --vz-danger-bg-subtle: #fce7e7;
                --vz-light-bg-subtle: #f9fbfc;
                --vz-dark-bg-subtle: #e9ebec;
                --vz-primary-border-subtle: #c2d3f5;
                --vz-secondary-border-subtle: #cfbef3;
                --vz-success-border-subtle: #a1e8c4;
                --vz-info-border-subtle: #b9e7f5;
                --vz-warning-border-subtle: #f6e4ba;
                --vz-danger-border-subtle: #f8bfbf;
                --vz-light-border-subtle: #eff2f7;
                --vz-dark-border-subtle: #adb5bd;
                --vz-white-rgb: 255, 255, 255;
                --vz-black-rgb: 0, 0, 0;
                --vz-font-sans-serif: "Rubik", sans-serif;
                --vz-font-monospace: SFMono-Regular, Menlo, Monaco, Consolas, "Liberation Mono", "Courier New", monospace;
                --vz-gradient: linear-gradient(180deg, rgba(255, 255, 255, 0.15), rgba(255, 255, 255, 0));
                --vz-body-font-family: var(--vz-font-sans-serif);
                --vz-body-font-size: 0.825rem;
                --vz-body-font-weight: 400;
                --vz-body-line-height: 1.5;
                --vz-body-color: #212529;
                --vz-body-color-rgb: 33, 37, 41;
                --vz-body-bg: #f3f6f9;
                --vz-body-bg-rgb: 243, 246, 249;
                --vz-emphasis-color: #212529;
                --vz-emphasis-color-rgb: 33, 37, 41;
                --vz-secondary-color: #878a99;
                --vz-secondary-color-rgb: 135, 138, 153;
                --vz-secondary-bg: #fff;
                --vz-secondary-bg-rgb: 255, 255, 255;
                --vz-tertiary-color: rgba(33, 37, 41, 0.5);
                --vz-tertiary-color-rgb: 33, 37, 41;
                --vz-tertiary-bg: #eff2f7;
                --vz-tertiary-bg-rgb: 239, 242, 247;
                --vz-heading-color: #495057;
                --vz-link-color: #6691e7;
                --vz-link-color-rgb: 102, 145, 231;
                --vz-link-decoration: none;
                --vz-link-hover-color: #6691e7;
                --vz-link-hover-color-rgb: 102, 145, 231;
                --vz-code-color: #f672a7;
                --vz-highlight-bg: #fcf8e3;
                --vz-border-width: 1px;
                --vz-border-style: solid;
                --vz-border-color: #e9ebec;
                --vz-border-color-translucent: #ced4da;
                --vz-border-radius: 0.25rem;
                --vz-border-radius-sm: 0.2rem;
                --vz-border-radius-lg: 0.3rem;
                --vz-border-radius-xl: 1rem;
                --vz-border-radius-xxl: 2rem;
                --vz-border-radius-2xl: var(--vz-border-radius-xxl);
                --vz-border-radius-pill: 50rem;
                --vz-box-shadow: 0 1px 2px rgba(56, 65, 74, 0.15);
                --vz-box-shadow-sm: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                --vz-box-shadow-lg: 0 5px 10px rgba(30, 32, 37, 0.12);
                --vz-box-shadow-inset: inset 0 1px 2px rgba(0, 0, 0, 0.075);
                --vz-focus-ring-width: 0.25rem;
                --vz-focus-ring-opacity: 0.25;
                --vz-focus-ring-color: rgba(var(--vz-primary-rgb), 0.25);
                --vz-form-valid-color: #13c56b;
                --vz-form-valid-border-color: #13c56b;
                --vz-form-invalid-color: #ed5e5e;
                --vz-form-invalid-border-color: #ed5e5e;
            }
            body {
                background-color: white
                /* background-color: transparent!important */
            }
            .card {
                background-color: white
                /* background-color: transparent!important */
            }
            .card {
                background-color: white
                /* background-color: transparent!important */
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
                                        <h6><span class="text-muted fw-normal">Email:</span><a href="mailto:support@italiancoffee-co.com" class="link-primary" target="_blank" id="email">support@italiancoffee-co.com</a></h6>
                                        <h6><span class="text-muted fw-normal">Website:</span> <a href="https://italiancoffee-co.com/" class="link-primary" target="_blank" id="website">www.italiancoffee-co.com</a></h6>
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
                                    <div class="col-lg-3 col-6">
                                        <p class="text-muted mb-2 text-uppercase fw-semibold">Total Amount</p>
                                        <h5 class="fs-14 mb-0">
                                            <span id="total-amount">
                                                <span class="cart-total-price flip-symbol text-left">
                                                    <span class="amount">{{ number_format($subTotal + $orderData->shipping_amount + ($orderData->total_amount_iqd - $subTotal), 0)}} </span>
                                                    <span class="currency">{{ __('IQD') }}</span>
                                                </span>
                                            </span>
                                        </h5>
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
                                    <button type="button" wire:click="printCustomPdf('{{ $orderData->tracking_number }}')" class="btn btn-success"><i class="ri-printer-line align-bottom me-1"></i> {{__('Print')}} </button>
                                    <a href="javascript:window.print()" class="btn btn-primary"><i class="ri-printer-line align-bottom me-1"></i> {{__('Quick Print')}}</a>
                                    {{-- <button type="button" wire:click="printDirectPdf('{{ $orderData->tracking_number }}')" class="btn btn-primary"><i class="ri-download-2-line align-bottom me-1"></i> Download</button> --}}
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
    <x-super-admins.components.pdf-order-invoice />
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
</div><!-- End Page-content -->

