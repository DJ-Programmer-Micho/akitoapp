<?php

namespace App\Http\Controllers\Pdf;

use App\Models\Order;
use App\Http\Controllers\Controller;

class PdfController extends Controller
{
    public function pdfOrderInvoice($local, $invoiceTracking)
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->where('tracking_number',$invoiceTracking)->first();
        // Return view with data
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total_iqd;
        }

        return view('super-admins.pdf.orderinvoice.order-invoice-print',[
            'orderData' => $order,
            'subTotal' => $sum,
        ]);    
    }

    public function pdfOrderAction($local, $invoiceTracking)
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->where('tracking_number',$invoiceTracking)->first();
        // Return view with data
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total_iqd;
        }

        return view('super-admins.pdf.orderinvoice.order-invoice-action-print',[
            'orderData' => $order,
            'subTotal' => $sum,
        ]);    
    }

    public function pdfOrderView($local, $invoiceTracking)
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->where('tracking_number',$invoiceTracking)->first();
        // Return view with data
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total_iqd;
        }

        return view('super-admins.pdf.orderinvoice.order-invoice-action-print',[
            'orderData' => $order,
            'subTotal' => $sum,
        ]);    
    }

    public function pdfOrderViewCancel($local, $invoiceTracking)
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->where('tracking_number',$invoiceTracking)->first();
        // Return view with data
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total_iqd;
        }

        if($order->status == 'canceled') {
            return view('super-admins.pdf.orderinvoice.order-invoice-action-print-cancelled',[
                'orderData' => $order,
                'subTotal' => $sum,
            ]);    
        } else{
            return redirect()->route('business.account', ['locale' => app()->getLocale()]);
        }
    }
}