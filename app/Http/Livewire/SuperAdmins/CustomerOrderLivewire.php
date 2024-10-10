<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\Order;
use Livewire\Component;
use App\Models\Customer;

class CustomerOrderLivewire extends Component
{
    public $oPending = 0;
    public $oShipping = 0;
    public $oDelivered = 0;
    public $oCancelled = 0;
    public $oRefunded = 0;
    public $oAll = 0;

    public $searchTerm;
    public $startDate;
    public $endDate;

    public $fullName;

    public $pAll = 0;
    public $pPending = 0;
    public $pPayed = 0;
    public $pFailed = 0;

    public $cust_id;

    public $search = '';
    public $statusFilter = 'all';
    public $statusPaymentFilter = 'all';
    public $statusPaymentMethodFilter = 'all';
    public $page = 1;

    public function mount($id)
    {
        $this->cust_id = $id;
        $this->fullName = Customer::with('customer_profile')->where('id', $id)->first()->customer_profile->only('first_name', 'last_name');
        $this->statusFilter = request()->query('statusFilter', 'all');
        $this->statusFilter = request()->query('statusPaymentFilter', 'all');
        $this->page = request()->query('page', 1);
    }

    public function changeTab($status)
    {
        $this->statusFilter = $status;
        $this->page = 1;
        $this->emitSelf('refresh');
    }

    public function updateStatus(int $id, $status)
    {
        // Find the brand by ID, if not found return an error
        $orderStatus = Order::find($id);
    
        if ($orderStatus) {
            // Toggle the status (0 to 1 and 1 to 0)
            $orderStatus->status = $status;
            $orderStatus->save();
    
            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Status Updated Successfully')
            ]);
        } else {
            // Dispatch a browser event to show error message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }

    public function render()
    {
        $locale = app()->getLocale();
        // Base query with eager loading
        $query = Order::where('customer_id', $this->cust_id)->with([
            'orderItems.product' => function ($query) use ($locale) {
                $query->with([
                    'productTranslation' => function ($subQuery) use ($locale) {
                        $subQuery->where('locale', $locale);
                    },
                    'variation',
                    'variation.images'
                ]);
            }
        ]);
    
        // Apply status filter (skip "all" option)
        $statusFilters = ['pending', 'shipping', 'delivered', 'canceled', 'refunded'];
        if (in_array($this->statusFilter, $statusFilters)) {
            $query->where('status', $this->statusFilter);
        }
    
        // Apply payment status filter
        if ($this->statusPaymentFilter !== 'all') {
            $query->where('payment_status', $this->statusPaymentFilter);
        }
    
        // Apply payment method filter
        if ($this->statusPaymentMethodFilter !== 'all') {
            $query->where('payment_method', $this->statusPaymentMethodFilter);
        }
    
        // Apply search filter
        if (!empty($this->searchTerm)) {
            $query->where(function ($subQuery) {
                $subQuery->where('id', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('tracking_number', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('first_name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('last_name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('email', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('country', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('city', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('address', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('phone_number', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('status', 'like', '%' . $this->searchTerm . '%');
            });
        }

        if (!empty($this->startDate)) {
            $query->whereDate('created_at', '>=', $this->startDate);            
        } 
        
        if  (!empty($this->endDate)) {
            $query->whereDate('created_at', '<=', $this->endDate);
        }

            // Clone the query for count calculation
        $filteredQuery = clone $query;

        // Get the counts after applying the filters
        $orderCounts = $filteredQuery->selectRaw("
            COUNT(CASE WHEN status = 'pending' THEN 1 END) as pending_count,
            COUNT(CASE WHEN status = 'shipping' THEN 1 END) as shipping_count,
            COUNT(CASE WHEN status = 'delivered' THEN 1 END) as delivered_count,
            COUNT(CASE WHEN status = 'canceled' THEN 1 END) as cancelled_count,
            COUNT(CASE WHEN status = 'refunded' THEN 1 END) as refunded_count,
            COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as payment_pending_count,
            COUNT(CASE WHEN payment_status = 'successful' THEN 1 END) as payment_successful_count,
            COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as payment_failed_count
        ")->first();

        // Set the count values
        $this->oPending = $orderCounts->pending_count;
        $this->oShipping = $orderCounts->shipping_count;
        $this->oDelivered = $orderCounts->delivered_count;
        $this->oCancelled = $orderCounts->cancelled_count;
        $this->oRefunded = $orderCounts->refunded_count;

        $this->pPending = $orderCounts->payment_pending_count;
        $this->pPayed = $orderCounts->payment_successful_count;
        $this->pFailed = $orderCounts->payment_failed_count;

        $this->oAll = $this->oPending + $this->oShipping + $this->oDelivered + $this->oCancelled + $this->oRefunded;
        $this->pAll = $this->pPending + $this->pPayed + $this->pFailed;
        
        // Paginate the results
        $orders = $query->paginate(15);
        // Return view with data
        return view('super-admins.pages.customerorder.customer-order-table', [
            'orderTable' => $orders,
        ]);
    }

}