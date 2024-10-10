<?php

namespace App\Http\Controllers\Pdf;

use App\Models\Order;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PdfController extends Controller
{
    public function pdfOrderInvoice($local, $invoiceTracking)
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->where('tracking_number',$invoiceTracking)->first();
        // Return view with data
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total;
        }

        return view('super-admins.pdf.orderinvoice.order-invoice-print',[
            'orderData' => $order,
            'subTotal' => $sum,
        ]);    
    }
}