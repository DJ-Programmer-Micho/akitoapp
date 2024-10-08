<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Order;
use Livewire\Component;

class OrderViewerLivewire extends Component
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

    public function updateStatus(int $id)
    {
        // Find the order by ID
        $order = Order::find($id);
    
        if ($order) {
            // Ensure that the selected payment status is not empty
            if (!empty($this->statusPaymentFilter)) {
                // Update the payment status with the selected filter value
                $order->payment_status = $this->statusPaymentFilter;
                $order->save();
    
                // Dispatch a success message
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'success',
                    'message' => __('Status Updated Successfully')
                ]);
            } else {
            }
        } else {
            // Dispatch an error message if the order is not found
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function render()
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->find($this->o_id);
        // Return view with data
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total;
        }
        
        return view('super-admins.pages.orderviewer.order-viewer', [
            'orderData' => $order,
            'subTotal' => $sum,
        ]);
    }
}