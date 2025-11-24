<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use Livewire\Component;
use App\Models\Transaction;
use Illuminate\Support\Str;
use App\Services\WalletService;
use App\Events\EventDriverUpdated;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Services\OrderRefundService;
use Illuminate\Support\Facades\Mail;
use App\Events\EventOrderStatusUpdated;
use App\Notifications\NotifyDriverUpdate;
use App\Mail\EmailInvoiceActionFailedMail;
use App\Events\EventOrderPaymentStatusUpdated;
use App\Notifications\NotifyOrderStatusChanged;
use App\Notifications\NotifyOrderPaymentStatusChanged;
use App\Models\Payment;       // your app Payments table (not gateway)

class OrderViewerLivewire extends Component
{
    public $o_id;

    public $searchTerm;
    public $startDate;
    public $endDate;
    public $statusPaymentFilter;
    public $statusFilter;

    public $driverList = [];
    public $selectedDriver;
    public $carModel;
    public $plateNumber;

    protected $listeners = [
        'echo:AdminChannel,EventOrderStatusUpdated' => 'statusReload',
        'echo:AdminChannel,EventDriverUpdated' => 'driverReload',
        'echo:AdminChannel,EventOrderPaymentStatusUpdated' => 'paymentReload',
    ];

    public function mount($id)
    {
        $this->o_id = $id;
        $this->getList();
    }

    public function updatePaymentStatus(int $id)
    {
        $order = Order::with('orderItems', 'customer.wallet')->find($id);

        if (!$order) {
            $this->toast('error', __('Record Not Found'));
            return;
        }
        if (empty($this->statusPaymentFilter)) {
            $this->toast('error', __('Select a payment status first'));
            return;
        }

        $new = $this->statusPaymentFilter;      // expected: pending|successful|failed|refunded|partially_refunded
        $old = $order->payment_status;

        DB::beginTransaction();
        try {
            if ($new === 'refunded') {
                $this->performRefund($order); // credit wallet, set payment_status=refunded, maybe status=refunded
            } else {
                // moving away from refunded? reverse wallet credit (if any)
                if ($old === 'refunded') {
                    $this->reverseRefund($order); // debit wallet, reduce refunded_minor
                }

                // set new payment status (must be valid enum)
                $order->payment_status = $new;
                $order->save();
            }

            DB::commit();

            $this->notifyPaymentStatus($order);
            try {
                broadcast(new EventOrderPaymentStatusUpdated($order->tracking_number, $order->payment_status))
                    ->toOthers();
            } catch (\Throwable $e) {
                $this->toast('info', __('Your Internet is Weak!: ') . $e->getMessage());
            }

            $this->toast('success', __('Status Updated Successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin Payment Status Update Error: ' . $e->getMessage(), ['order_id' => $order->id ?? null]);
            $this->toast('error', __('Update failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    /**
     * SHIPPING STATUS UPDATE
     * Supports: pending | shipping | delivered | cancelled | refunded
     */
    public function updateStatus(int $id)
    {
        $order = Order::with('orderItems', 'customer.wallet')->find($id);

        if (!$order) {
            $this->toast('error', __('Record Not Found'));
            return;
        }
        if (empty($this->statusFilter)) {
            $this->toast('error', __('Select a status first'));
            return;
        }

        $new = $this->statusFilter; // pending|shipping|delivered|cancelled|refunded
        $old = $order->status;

        DB::beginTransaction();
        try {
            if ($new === 'refunded') {
                $this->performRefund($order); // also sets status=refunded
            } else {
                // leaving refunded while payment is refunded? reverse the refund
                if ($old === 'refunded' || $order->payment_status === 'refunded') {
                    $this->reverseRefund($order);
                }

                // business rule: cancelled → payment_status=failed (if not already successful/refunded)
                if ($new === 'cancelled' && !in_array($order->payment_status, ['successful', 'refunded'], true)) {
                    $order->payment_status = 'failed';
                    Mail::to($order->customer->email)->queue(new EmailInvoiceActionFailedMail($order));
                }

                $order->status = $new;
                $order->save();
            }

            DB::commit();

            $this->notifyShippingStatus($order);
            try {
                broadcast(new EventOrderStatusUpdated($order->tracking_number, $order->status))->toOthers();
            } catch (\Throwable $e) {
                $this->toast('info', __('Your Internet is Weak!: ') . $e->getMessage());
            }

            $this->toast('success', __('Status Updated Successfully'));
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin Shipping Status Update Error: ' . $e->getMessage(), ['order_id' => $order->id ?? null]);
            $this->toast('error', __('Update failed: :msg', ['msg' => $e->getMessage()]));
        }
    }


    /** -------------------------
     *  REFUND CORE (items + shipping only)
     *  ------------------------- */
    private function performRefund(Order $order): void
    {
        // amounts
        $itemsSubtotal = (int) $order->orderItems->sum('total_iqd'); // minor IQD
        $shipping      = (int) ($order->shipping_amount ?? 0);
        $refundableBase = $itemsSubtotal + $shipping;                // exclude fees

        $alreadyRefunded = (int) ($order->refunded_minor ?? 0);
        $paid            = (int) ($order->paid_minor ?? 0);
        $maxRefundable   = max(0, $paid - $alreadyRefunded);
        $toRefund        = max(0, min($refundableBase, $maxRefundable));

        if ($toRefund <= 0) {
            throw new \RuntimeException(__('Nothing to refund'));
        }

        // credit wallet
        $wallet = $order->customer->wallet()->lockForUpdate()->firstOrCreate(['currency' => 'IQD']);
        app(WalletService::class)->credit($wallet, $toRefund, [
            'reason' => 'order_refund_excluding_fees',
            'meta'   => [
                'order_id'  => $order->id,
                'tracking'  => $order->tracking_number,
                'items_iqd' => $itemsSubtotal,
                'shipping'  => $shipping,
                'fees'      => max(0, ($order->total_minor ?? 0) - $itemsSubtotal - $shipping),
            ],
        ]);

        // reflect in order
        $order->refunded_minor = $alreadyRefunded + $toRefund;
        $order->payment_status = 'refunded';
        if ($order->status !== 'delivered') {
            $order->status = 'refunded';
        }
        $order->save();

        // log refund as Payment + Transaction
        $payment = Payment::create([
            'order_id'           => $order->id,
            'amount_minor'       => $toRefund,
            'currency'           => 'IQD',
            'method'             => 'Wallet',
            'status'             => 'successful',
            'provider'           => 'Wallet',
            'provider_payment_id'=> null,
            'idempotency_key'    => Str::uuid(),
        ]);

        Transaction::create([
            'id'           => Str::uuid(),
            'payment_id'   => $payment->id,
            'order_id'     => $order->id,
            'provider'     => 'Wallet',
            'amount_minor' => $toRefund,
            'currency'     => 'IQD',
            'status'       => 'successful',
            'response'     => [
                'kind'   => 'refund',
                'note'   => 'Admin refund excluding fees',
                'source' => 'backoffice',
            ],
        ]);
    }

    /** -------------------------
     *  REVERSE REFUND (admin changed mind)
     *  ------------------------- */
    private function reverseRefund(Order $order): void
    {
        $itemsSubtotal = (int) $order->orderItems->sum('total_iqd');
        $shipping      = (int) ($order->shipping_amount ?? 0);
        $refunded      = (int) ($order->refunded_minor ?? 0);

        // attempt to reverse up to what was previously refundable (items+shipping),
        // but never exceed refunded_minor
        $maxReversible = min($refunded, $itemsSubtotal + $shipping);
        if ($maxReversible <= 0) {
            // nothing to reverse
            return;
        }

        $wallet = $order->customer->wallet()->lockForUpdate()->firstOrCreate(['currency' => 'IQD']);

        if ($wallet->balance_minor < $maxReversible) {
            // protect against accidental negative wallet
            throw new \RuntimeException(__('Cannot reverse refund: customer wallet balance (:bal) is less than refundable (:need)', [
                'bal'  => number_format($wallet->balance_minor, 0),
                'need' => number_format($maxReversible, 0),
            ]));
        }

        app(WalletService::class)->debit($wallet, $maxReversible, [
            'reason' => 'order_refund_reversal',
            'meta'   => [
                'order_id'  => $order->id,
                'tracking'  => $order->tracking_number,
            ],
        ]);

        $order->refunded_minor = $refunded - $maxReversible;
        // do NOT set payment_status here; caller will set the new status (pending/successful/failed)
        $order->save();

        // log reversal as Payment + Transaction
        $payment = Payment::create([
            'order_id'           => $order->id,
            'amount_minor'       => $maxReversible, // positive number; semantic = wallet debit
            'currency'           => 'IQD',
            'method'             => 'Wallet',
            'status'             => 'successful',
            'provider'           => 'Wallet',
            'provider_payment_id'=> null,
            'idempotency_key'    => Str::uuid(),
        ]);

        Transaction::create([
            'id'           => Str::uuid(),
            'payment_id'   => $payment->id,
            'order_id'     => $order->id,
            'provider'     => 'Wallet',
            'amount_minor' => $maxReversible,
            'currency'     => 'IQD',
            'status'       => 'successful',
            'response'     => [
                'kind'   => 'refund_reversal',
                'note'   => 'Admin switched away from refunded',
                'source' => 'backoffice',
            ],
        ]);
    }

    /** -------------------------
     *  NOTIFY/BROADCAST HELPERS
     *  ------------------------- */
    private function notifyPaymentStatus(Order $order): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Administrator')
                ->orWhere('name', 'Data Entry Specialist')
                ->orWhere('name', 'Finance Manager')
                ->orWhere('name', 'Order Processor');
        })->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        foreach ($adminUsers as $admin) {
            if (!$admin->notifications()
                ->where('data->order_id', $order->tracking_number)
                ->where('data->status', $order->payment_status)
                ->exists()) {
                $admin->notify(new NotifyOrderPaymentStatusChanged(
                    $order->tracking_number,
                    $order->id,
                    $order->payment_status,
                    "Order ID [#{$order->tracking_number}] Payment has been updated to {$order->payment_status}",
                ));
            }
        }

        if ($this->selectedDriver) {
            $driverUser = User::find($this->selectedDriver);
            if ($driverUser) {
                $driverUser->notify(new NotifyOrderPaymentStatusChanged(
                    $order->tracking_number,
                    $order->id,
                    $order->payment_status,
                    "Order ID [#{$order->tracking_number}] Payment has been updated to {$order->payment_status}!"
                ));
            }
        }
    }

    private function notifyShippingStatus(Order $order): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->where('name', 'Administrator')
                ->orWhere('name', 'Data Entry Specialist')
                ->orWhere('name', 'Finance Manager')
                ->orWhere('name', 'Order Processor');
        })->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        foreach ($adminUsers as $admin) {
            if (!$admin->notifications()
                ->where('data->order_id', $order->tracking_number)
                ->where('data->status', $order->status)
                ->exists()) {
                $admin->notify(new NotifyOrderStatusChanged(
                    $order->tracking_number,
                    $order->id,
                    $order->status,
                    "Order ID {$order->tracking_number} has been updated to {$order->status}",
                ));
            }
        }

        if ($this->selectedDriver) {
            $driverUser = User::find($this->selectedDriver);
            if ($driverUser) {
                $driverUser->notify(new NotifyOrderStatusChanged(
                    $order->tracking_number,
                    $order->id,
                    $order->status,
                    "Order ID {$order->tracking_number} has been updated to {$order->status}"
                ));
            }
        }
    }

    /** -------------------------
     *  UI / OTHER methods
     *  ------------------------- */
    private function toast($type, $message)
    {
        $this->dispatchBrowserEvent('alert', ['type' => $type, 'message' => $message]);
    }

    public function driverDataInit()
    {
        $driverData = User::where('id', $this->selectedDriver)->first();
        $this->carModel    = $driverData->driver->vehicle_model ?? 'N/A';
        $this->plateNumber = $driverData->driver->plate_number ?? 'N/A';
    }

    public function driverData()
    {
        $driverData = User::where('id', $this->selectedDriver)->first();
        $this->carModel    = $driverData->driver->vehicle_model ?? 'N/A';
        $this->plateNumber = $driverData->driver->plate_number ?? 'N/A';

        try {
            $order = Order::where('id', $this->o_id)->first(['id','tracking_number']);

            Order::where('id', $this->o_id)->update(['driver' => $driverData->id]);

            $adminUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Administrator')
                    ->orWhere('name', 'Data Entry Specialist')
                    ->orWhere('name', 'Finance Manager')
                    ->orWhere('name', 'Order Processor');
            })->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Driver');
            })->get();

            foreach ($adminUsers as $admin) {
                if (!$admin->notifications()
                    ->where('data->order_id', $order->tracking_number)
                    ->where('data->driverId', $driverData->id)->exists()) {
                    $admin->notify(new NotifyDriverUpdate(
                        $order->tracking_number,
                        $order->id,
                        $driverData->id,
                        "Order ID: [#{$order->tracking_number}] has been Transferred to {$driverData->profile->first_name} {$driverData->profile->last_name}"
                    ));
                }
            }

            if ($this->selectedDriver) {
                $driverUser = User::find($this->selectedDriver);
                $driverUser?->notify(new NotifyDriverUpdate(
                    $order->tracking_number,
                    $order->id,
                    $driverData->id,
                    "Order ID: [#{$order->tracking_number}] has been Transferred to {$driverData->profile->first_name} {$driverData->profile->last_name}"
                ));
            }

            try {
                broadcast(new EventDriverUpdated($order->tracking_number, $driverData->profile->first_name .' '. $driverData->profile->last_name))->toOthers();
            } catch (\Exception $e) {
                $this->toast('info', __('Your Internet is Weak!: ' . $e->getMessage()));
            }
            $this->toast('success', __('Driver Updated'));
        } catch (\Exception $e) {
            $this->toast('error', __('U-Error: ' . $e->getMessage()));
        }
    }

    private function getList()
    {
        $orderLocation = Order::where('id', $this->o_id)->first(['latitude', 'longitude','driver']);
        if (!$orderLocation) return [];

        if ($orderLocation->driver) {
            $this->selectedDriver = $orderLocation->driver;
            $this->driverDataInit();
        }

        $latitude  = $orderLocation->latitude;
        $longitude = $orderLocation->longitude;

        $zones = Zone::where('status', 1)->get()
            ->filter(fn($zone) => $this->isWithinZone($zone->coordinates, $latitude, $longitude));

        $teamsInZone = $zones->pluck('delivery_team')->toArray();

        $driversInZone = User::whereHas('roles', fn($q) => $q->where('roles.id', 8))
            ->whereHas('driverTeam', fn($q) => $q->whereIn('driver_teams.id', $teamsInZone))
            ->get();

        $driversOutOfZone = User::whereHas('roles', fn($q) => $q->where('roles.id', 8))
            ->whereDoesntHave('driverTeam', fn($q) => $q->whereIn('driver_teams.id', $teamsInZone))
            ->get();

        $this->driverList = [
            [
                "text" => "Drivers in the Zone",
                "children" => $driversInZone->map(fn($d) => ["id" => $d->id, "driverName" => $d->profile->first_name])->toArray()
            ],
            [
                "text" => "Drivers outside the Zone",
                "children" => $driversOutOfZone->map(fn($d) => ["id" => $d->id, "driverName" => $d->username])->toArray()
            ]
        ];
    }

    private function isWithinZone($zoneCoordinates, $latitude, $longitude)
    {
        Log::info('Raw coordinates: ', ['zoneCoordinates' => $zoneCoordinates]);
        $coordinates = json_decode($zoneCoordinates, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            Log::error('JSON decode error: ' . json_last_error_msg());
            throw new \Exception("Invalid coordinates format");
        }
        if (!is_array($coordinates) || count($coordinates) < 3) {
            throw new \Exception("Invalid coordinates format");
        }
        foreach ($coordinates as $coordinate) {
            if (!isset($coordinate['lat'], $coordinate['lng'])) {
                throw new \Exception("Coordinate must contain 'lat' and 'lng' keys");
            }
            if ($this->isCoordinateInsidePolygon($coordinates, $latitude, $longitude)) {
                return true;
            }
        }
        return false;
    }

    private function isCoordinateInsidePolygon($coordinates, $lat, $lng)
    {
        $inside = false;
        $num = count($coordinates);
        for ($i = 0, $j = $num - 1; $i < $num; $j = $i++) {
            $v1 = $coordinates[$i];
            $v2 = $coordinates[$j];
            if (!isset($v1['lat'], $v1['lng']) || !isset($v2['lat'], $v2['lng'])) {
                throw new \Exception("Invalid coordinate points in polygon");
            }
            if (($v1['lat'] > $lat) != ($v2['lat'] > $lat) &&
                ($lng < ($v2['lng'] - $v1['lng']) * ($lat - $v1['lat']) / ($v2['lat'] - $v1['lat']) + $v1['lng'])) {
                $inside = !$inside;
            }
        }
        return $inside;
    }

    public function render()
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->find($this->o_id);
        foreach($order->orderItems as $item) {
            $sum += $item->total_iqd;
        }
        return view('super-admins.pages.orderviewer.order-viewer', [
            'orderData' => $order,
            'subTotal'  => $sum,
        ]);
    }

    public function driverReload()
    {
        $orderLocation = Order::where('id', $this->o_id)->first(['latitude', 'longitude','driver']);
        if (!$orderLocation) return [];
        if ($orderLocation->driver) {
            $this->selectedDriver = $orderLocation->driver;
            $this->driverData();
        }
        $this->emit('notificationSound');
    }

    public function statusReload()
    {
        $orderLocation = Order::where('id', $this->o_id)->first(['status']);
        if (!$orderLocation) return [];
        if ($orderLocation->status) {
            $this->statusFilter = $orderLocation->status;
        }
        $this->emit('notificationSound');
    }

    public function paymentReload()
    {
        $orderLocation = Order::where('id', $this->o_id)->first(['payment_status']);
        if (!$orderLocation) return [];
        if ($orderLocation->payment_status) {
            $this->statusPaymentFilter = $orderLocation->payment_status;
        }
        $this->emit('notificationSound');
    }
}
