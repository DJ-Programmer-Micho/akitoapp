<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\User;
use App\Models\Order;
use App\Models\Refund;
use Livewire\Component;
use Livewire\WithPagination;
use App\Services\WalletService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\OrderRefundService;
use Illuminate\Support\Facades\Mail;
use App\Events\EventOrderStatusUpdated;
use App\Mail\EmailInvoiceActionFailedMail;
use App\Notifications\NotifyOrderStatusChanged;

class OrderLivewire extends Component
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
    public $pRefunded = 0;

    public $search = '';
    public $statusFilter = 'all';
    public $statusPaymentFilter = 'all';
    public $statusPaymentMethodFilter = 'all';
    public $page = 1;

    protected $listeners = [
        'echo:AdminChannel,EventOrderStatusUpdated' => 'reloadTable',
        'echo:AdminChannel,EventOrderPaymentStatusUpdated' => 'reloadTable',
    ];

    public function reloadTable($e){ 
        $this->emit('notificationSound');
        // $this->render();
        $this->emitSelf('$refresh');
    }

    public function mount()
    {
        $user = auth('admin')->user();

        if (!$user) {
            abort(403, 'Unauthorized action.');
        }
        $this->statusFilter = request()->query('statusFilter', 'all');
        $this->statusPaymentFilter = request()->query('statusPaymentFilter', 'all');
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
    $orderStatus = Order::with('orderItems', 'customer.wallet')->find($id);
    if (!$orderStatus) {
        $this->dispatchBrowserEvent('alert', ['type' => 'error','message' => __('Record Not Found')]);
        return;
    }

    try {
        if ($status === 'refunded') {
            app(OrderRefundService::class)->performRefund($orderStatus, 'order_table_refund');
        } else {
            // leaving refunded?
            if ($orderStatus->status === 'refunded' || $orderStatus->payment_status === 'refunded') {
                app(OrderRefundService::class)->reverseRefund($orderStatus, 'order_table_refund_reverse');
            }

            if ($status === 'cancelled' && $orderStatus->payment_status === 'pending') {
                $orderStatus->payment_status = 'failed';
            }

            $orderStatus->status = $status;
            $orderStatus->save();
        }
        
    
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Administrator')
                      ->orWhere('name', 'Data Entry Specialist')
                      ->orWhere('name', 'Finance Manager')
                      ->orWhere('name', 'Order Processor');
            })->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Driver');
            })->get();

            foreach ($adminUsers as $admin) {
                if (!$admin->notifications()->where('data->order_id', $orderStatus->tracking_number)
                    ->where('data->status', $orderStatus->status)->exists()) {
                    $admin->notify(new NotifyOrderStatusChanged(
                        $orderStatus->tracking_number, 
                        $orderStatus->id,
                        $orderStatus->status, 
                        "Order ID {$orderStatus->tracking_number} has been updated to {$orderStatus->status}", 
                    ));
                }
            }

            // Notify specific driver
            if($orderStatus->driver) {
                $driverUser = User::find($orderStatus->driver);
                $driverUser->notify(new NotifyOrderStatusChanged(
                    $orderStatus->tracking_number,
                    $orderStatus->status,
                    $orderStatus->id,
                    "Order ID {$orderStatus->tracking_number} has been updated to {$orderStatus->status}",
                ));
            }

            // Broadcast to admins and the specific driver
            try {
                broadcast(new EventOrderStatusUpdated($orderStatus->tracking_number, $orderStatus->id, $orderStatus->status))->toOthers();    
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('Your Internet is Weak!: ' . $e->getMessage())]);
                return;
            }
            
            if($status == 'cancelled') {
                Mail::to($orderStatus->customer->email)->queue(new EmailInvoiceActionFailedMail($orderStatus));
            }

            // Dispatch a browser event to show success message
            $this->dispatchBrowserEvent('alert', [
                'type' => 'success',
                'message' => __('Status Updated Successfully')
            ]);
        } catch (\Throwable $e) {
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Update failed: :msg', ['msg' => $e->getMessage()])
            ]);
        }
    }

    private function refundOrderToWallet(Order $order, string $reason = 'admin_action'): void
    {
        // If already refunded, skip
        if (in_array($order->payment_status, ['refunded','partially_refunded'])) {
            return;
        }

        DB::transaction(function () use ($order, $reason) {
            $customer = $order->customer;
            $wallet   = $customer->wallet()->lockForUpdate()->firstOrCreate([
                'currency' => $order->currency ?? 'IQD',
            ]);

            // Sum all successful digital payments (exclude COD)
            $successfulPayments = $order->payments()
                ->where('status', 'successful')
                ->where('method', '!=', 'Cash On Delivery')
                ->get();

            if ($successfulPayments->isEmpty()) {
                // no captured digital payment => nothing to refund to wallet
                $order->payment_status = $order->payment_status === 'successful' ? 'refunded' : 'failed';
                $order->save();
                return;
            }

            // Sum decimal amounts (IQD) -> integer minor units
            $totalRefundMinor = 0;
            foreach ($successfulPayments as $p) {
                $minor = (int) round($p->amount); // IQD has 0 decimals; safe cast
                $totalRefundMinor += $minor;
            }

            if ($totalRefundMinor <= 0) {
                return;
            }

            // Credit wallet (with ledger)
            app(WalletService::class)->credit($wallet, $totalRefundMinor, [
                'source_type' => Order::class,
                'source_id'   => $order->id,
                'reason'      => 'order_refund',
                'meta'        => ['tracking' => $order->tracking_number, 'by' => 'admin', 'why' => $reason],
            ]);

            // Create Refund rows per payment (ledger)
            foreach ($successfulPayments as $p) {
                Refund::create([
                    'payment_id'   => $p->id,
                    'destination'  => 'wallet',
                    'amount_minor' => (int) round($p->amount),
                    'currency'     => $order->currency ?? 'IQD',
                    'status'       => 'processed',
                    'reason'       => $reason,
                    'processed_at' => now(),
                    'metadata'     => ['order_id' => $order->id, 'tracking' => $order->tracking_number],
                ]);
            }

            // Update order money fields
            $order->refunded_minor = ($order->refunded_minor ?? 0) + $totalRefundMinor;
            $order->payment_status = ($order->refunded_minor > 0) ? 'refunded' : $order->payment_status;
            $order->save();
        });
    }

    public function render()
    {
        // $this->emit('notificationSound');
        $locale = app()->getLocale();
        // Base query with eager loading
        $query = Order::with([
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
        $statusFilters = ['pending', 'shipping', 'delivered', 'cancelled', 'refunded'];
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
            COUNT(CASE WHEN status = 'cancelled' THEN 1 END) as cancelled_count,
            COUNT(CASE WHEN status = 'refunded' THEN 1 END) as refunded_count,
            COUNT(CASE WHEN payment_status = 'pending' THEN 1 END) as payment_pending_count,
            COUNT(CASE WHEN payment_status = 'successful' THEN 1 END) as payment_successful_count,
            COUNT(CASE WHEN payment_status = 'failed' THEN 1 END) as payment_failed_count,
            COUNT(CASE WHEN payment_status = 'refunded' THEN 1 END) as payment_refunded_count
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
        $this->pRefunded = $orderCounts->payment_refunded_count ?? 0;

        $this->oAll = $this->oPending + $this->oShipping + $this->oDelivered + $this->oCancelled + $this->oRefunded;
        // $this->pAll = $this->pPending + $this->pPayed + $this->pFailed;
        $this->pAll = $this->pPending + $this->pPayed + $this->pFailed + $this->pRefunded;
        // Paginate the results
        $orders = $query->orderBy('created_at', 'DESC')->paginate(15);
        // Return view with data
        return view('super-admins.pages.order.order-table', [
            'orderTable' => $orders,
        ]);
    }

}