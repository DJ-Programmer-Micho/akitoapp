<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Invoice of #VL{{$orderData->tracking_number}}</title>
    <link href="https://italiancoffee-co.com/dashboard/css/bootstrap.min.css" rel="stylesheet" type="text/css" />
    <!-- Icons Css -->
    <link href="https://italiancoffee-co.com/dashboard/css/icons.min.css" rel="stylesheet" type="text/css" />
    <!-- App Css-->
    <link href="https://italiancoffee-co.com/dashboard/css/app.min.css" rel="stylesheet" type="text/css" />
    <!-- custom Css-->
    <style>
        @media print {
            /* Hide everything by default */
            body > * {
                display: none !important; /* Hide all body children */
            }
        
            /* Show only the container */
            td.container {
                display: block !important; /* Ensure the container is displayed */
            }
        
            /* Optional: Additional adjustments */
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
        
            .text-muted {
                font-size: 10pt!important;
            }
        
            .card {
                page-break-inside: avoid; /* Avoid breaking the card across pages */
            }
        
            /* Adjust font sizes for better print readability */
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

        <div class="col-12">
            <table class="body-wrap" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; width: 100%; background-color: transparent; margin: 0;">
                <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                    <td style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; vertical-align: top; margin: 0;" valign="top"></td>
                    <td class="container" width="600" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; display: block !important; max-width: 600px !important; clear: both !important; margin: 0 auto;" valign="top">
                        <div class="content" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; max-width: 600px; display: block; margin: 0 auto; padding: 20px;">
                            <table class="main" width="100%" cellpadding="0" cellspacing="0" itemprop="action" itemscope itemtype="http://schema.org/ConfirmAction" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; border-radius: 3px; margin: 0; border: none;">
                                <tr style="font-family: 'Roboto', sans-serif; font-size: 14px; margin: 0;">
                                    <td class="content-wrap" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; color: #495057; font-size: 14px; vertical-align: top; margin: 0;padding: 30px; box-shadow: 0 3px 15px rgba(30,32,37,.06); ;border-radius: 7px; background-color: #fff;" valign="top">
                                        <meta itemprop="name" content="Confirm Email" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;" />
                                        <div  style="text-align: center; padding: 15px">
                                            <img src="{{ app('main_logo') }}" alt="Akito" width="140">
                                        </div>
                                        <table width="100%" cellpadding="0" cellspacing="0" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 24px; vertical-align: top; margin: 0; padding: 0 0 10px; text-align: center;" valign="top">
                                                    <h4 style="font-family: 'Roboto', sans-serif; margin-bottom: 10px; font-weight: 600;">Your order has been placed</h5>
                                                </td>
                                            </tr>
                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                    <h5 style="font-family: 'Roboto', sans-serif; margin-bottom: 3px;">Hey, {{$orderData->customer->customer_profile->first_name}} {{$orderData->customer->customer_profile->last_name}}</h5>
                                                    <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">Your order has been confirmed and will be shipping soon.</p>
                                                </td>
                                            </tr>
                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 18px;" valign="top">
                                                    <table style="width:100%;">
                                                        <tbody>
                                                            <tr style="text-align: left;">
                                                                <th style="padding: 5px;">
                                                                    <p style="color: #878a99; font-size: 13px; margin-bottom: 2px; font-weight: 400;">Order Number</p>
                                                                    <span>#{{$orderData->tracking_number}}</span>
                                                                </th>
                                                                <th style="padding: 5px;">
                                                                    <p style="color: #878a99; font-size: 13px; margin-bottom: 2px; font-weight: 400;">Order Date</p>
                                                                    <span>{{$orderData->created_at}}</span>
                                                                </th>
                                                                <th style="padding: 5px;">
                                                                    <p style="color: #878a99; font-size: 13px; margin-bottom: 2px; font-weight: 400;">Payment Status</p>
                                                                    @if ($orderData->payment_status == "pending")
                                                                    <span style="color: #4169e1 ">
                                                                        {{strtoupper($orderData->payment_status)}}
                                                                    </span>
                                                                    @elseif($orderData->payment_status == "successful")
                                                                    <span style="color: #136207 ">
                                                                        {{strtoupper($orderData->payment_status)}}
                                                                    </span>
                                                                    @else
                                                                    <span style="color: #cc0022">
                                                                        {{strtoupper($orderData->payment_status)}}
                                                                    </span>
                                                                    @endif
                                                                </th>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 12px;" valign="top">
                                                    <h6 style="font-family: 'Roboto', sans-serif; font-size: 15px; text-decoration-line: underline;margin-bottom: 15px;">Here what you ordered:</h6>
                                                    <table style="width:100%;" cellspacing="0" cellpadding="0">
                                                        <thead style="text-align: left;">
                                                            <th style="padding: 8px;border-bottom: 1px solid #e9ebec;">Product Details</th>
                                                            <th style="padding: 8px;border-bottom: 1px solid #e9ebec;">Quantity</th>
                                                            <th style="padding: 8px;border-bottom: 1px solid #e9ebec;">Amount</th>
                                                        </thead>
                                                        <tbody>
                                                            @foreach ($orderData->orderItems as $index => $item)
                                                            <tr>
                                                                <td style="padding: 8px; font-size: 13px;">
                                                                    <h6 style="margin-bottom: 2px; font-size: 14px;">{{$item->product_name}}</h6>
                                                                    {{-- <p style="margin-bottom: 2px; font-size: 13px; color: #878a99;">Graphic Print Men & Women Sweatshirt</p> --}}
                                                                </td>
                                                                <td style="padding: 8px; font-size: 13px;">
                                                                    {{$item->quantity}}
                                                                </td>
                                                                <td style="padding: 8px; font-size: 13px;">
                                                                    <span class="cart-total-price flip-symbol text-left">
                                                                        <span class="amount">{{ number_format($item->total_iqd, 0)}} </span>
                                                                        <span class="currency">{{ __('IQD') }}</span>
                                                                    </span>
                                                                </td>
                                                            </tr>
                                                            @endforeach
                                                            <tr>
                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                    Subtotal
                                                                </td>
                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                    <span class="cart-total-price flip-symbol text-left">
                                                                        <span class="amount">{{ number_format($subTotal, 0)}} </span>
                                                                        <span class="currency">{{ __('IQD') }}</span>
                                                                    </span>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;">
                                                                    Shipping Charge
                                                                </td>
                                                                <th style="padding: 8px; font-size: 13px;">
                                                                    <span class="cart-total-price flip-symbol text-left">
                                                                        <span class="amount" style="color: #44a6c4">{{ number_format($orderData->shipping_amount, 0)}} </span>
                                                                        <span class="currency" style="color: #44a6c4">{{ __('IQD') }}</span>
                                                                    </span>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;">
                                                                    Fee Amount
                                                                </td>
                                                                <th style="padding: 8px; font-size: 13px;">
                                                                    <span class="cart-total-price flip-symbol text-left">
                                                                        <span class="amount" style="color: #44a6c4">{{ number_format($orderData->total_amount_iqd - $subTotal, 0)}} </span>
                                                                        <span class="currency" style="color: #44a6c4">{{ __('IQD') }}</span>
                                                                    </span>
                                                                </th>
                                                            </tr>
                                                            <tr>
                                                                <td colspan="2" style="padding: 8px; font-size: 13px; text-align: end;border-top: 1px solid #e9ebec;">
                                                                    Total Amount
                                                                </td>
                                                                <th style="padding: 8px; font-size: 13px;border-top: 1px solid #e9ebec;">
                                                                    <span class="cart-total-price flip-symbol text-left">
                                                                        <span class="amount" style="color: #13c56b">{{ number_format($subTotal + $orderData->shipping_amount + ($orderData->total_amount_iqd - $subTotal), 0)}} </span>
                                                                        <span class="currency" style="color: #13c56b">{{ __('IQD') }}</span>
                                                                    </span>
                                                                </th>
                                                            </tr>
                                                        </tbody>
                                                    </table>
                                                </td>
                                            </tr>
                                            <tr style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 14px; margin: 0;">
                                                <td class="content-block" style="font-family: 'Roboto', sans-serif; box-sizing: border-box; font-size: 15px; vertical-align: top; margin: 0; padding: 0 0 0px;" valign="top">
                                                    <p style="font-family: 'Roboto', sans-serif; margin-bottom: 8px; color: #878a99;">Wl'll send you shipping confirmation when your item(s) are on the way! We appreciate your business, and hope you enjoy your purchase.</p>
                                                    <h6 style="font-family: 'Roboto', sans-serif; font-size: 14px; margin-bottom: 0px; text-align: end;">Thank you!</h6>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </td>
                </tr>
            </table>
            <!-- end table -->
        </div>
        <!--end row-->

    </div><!-- container-fluid -->
</div><!-- End Page-content -->
</body>
</html>
