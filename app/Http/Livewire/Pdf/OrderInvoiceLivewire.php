<?php

namespace App\Http\Livewire\Pdf;

use App\Models\Order;
use Livewire\Component;
use Barryvdh\DomPDF\Facade\Pdf;

class OrderInvoiceLivewire extends Component
{


    public $o_id;

    public $searchTerm;
    public $startDate;
    public $endDate;
    public $statusPaymentFilter;


    public function mount($id)
    {
        $this->o_id = $id;
    }

    public function printCustomPdf($invoiceId){
        try {
            $this->dispatchBrowserEvent('openPdfInNewTab', ['trackingId' => $invoiceId]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('PDF Error')]);
        }
    } 

    // direct pdf download?
    public function printDirectPdf($invoiceId)
    {
        try {
            $sum = 0;
            $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')
                          ->where('tracking_number', $invoiceId)
                          ->first();
    
            // Load the PDF view
            $pdf = Pdf::loadView('super-admins.pdf.orderinvoice.order-invoice-print', [
                'orderData' => $order,
                'subTotal' => $sum,
            ]);
    
            // Return the PDF as a download
            return $pdf->download("invoice_{$invoiceId}.pdf");
        } catch (\Exception $e) {
            // Log the error and provide feedback
            dd('PDF Generation Error: ' . $e->getMessage());
            return redirect()->back()->with('error', __('PDF Error: ' . $e->getMessage()));
        }
    }
    

    public function render()
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->where('tracking_number',$this->o_id)->first();
        // Return view with data
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total_iqd;
        }
        
        return view('super-admins.pdf.orderinvoice.order-invoice', [
            'orderData' => $order,
            'subTotal' => $sum,
        ]);
    }
}