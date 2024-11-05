<?php

namespace App\Http\Livewire\Driver;

use App\Models\Order;
use Livewire\Component;
use Livewire\WithPagination;

class DriverTaskLivewire extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    
    public $oPending = 0;
    public $oShipping = 0;
    public $oDelivered = 0;
    public $oCancelled = 0;
    public $oRefunded = 0;
    public $oAll = 0;

    public $searchTerm;
    public $startDate;
    public $endDate;

    public $pAll = 0;
    public $pPending = 0;
    public $pPayed = 0;
    public $pFailed = 0;

    public $search = '';
    public $statusFilter = 'all';
    public $statusPaymentFilter = 'all';
    public $statusPaymentMethodFilter = 'all';
    public $page = 1;

    protected $listeners = [
        'echo:AdminChannel,EventDriverUpdated' => 'reloadTable'
    ];

    public function reloadTable($e){ 
        $this->render();
    }

    public function mount()
    {
        // if (!auth('admin')->check() || !hasRole([8])) {
        //     return redirect()->back();
        //     dd('asd');
        // } else {
        //     dd('huh');
        // }
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
        $userId = auth('admin')->user()->id;
            $query = Order::with([
                'orderItems.product' => function ($query) use ($locale) {
                }
            ])->where('driver', $userId);
        
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

            // Paginate the results
        $orders = $query->paginate(15);
        // Return view with data
        return view('super-admins.pages.tasks.drivertasks.order-table', [
            'orderTable' => $orders,
        ]);
    }
}