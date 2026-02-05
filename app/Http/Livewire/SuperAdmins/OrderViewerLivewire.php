<?php

namespace App\Http\Livewire\SuperAdmins;

use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Transaction;

use App\Services\WalletService;

use App\Events\EventDriverUpdated;
use App\Events\EventOrderStatusUpdated;
use App\Events\EventOrderPaymentStatusUpdated;

use App\Notifications\NotifyDriverUpdate;
use App\Notifications\NotifyOrderStatusChanged;
use App\Notifications\NotifyOrderPaymentStatusChanged;

use App\Mail\EmailInvoiceActionFailedMail;

class OrderViewerLivewire extends Component
{
    public $o_id;

    public $statusPaymentFilter;
    public $statusFilter;

    public $driverList = [];
    public $selectedDriver;
    public $carModel = 'N/A';
    public $plateNumber = 'N/A';

    /** Cached order to avoid heavy reload on every Livewire request */
    public $orderData;

    protected $listeners = [
        'echo:AdminChannel,EventOrderStatusUpdated' => 'statusReload',
        'echo:AdminChannel,EventDriverUpdated' => 'driverReload',
        'echo:AdminChannel,EventOrderPaymentStatusUpdated' => 'paymentReload',
    ];

    public function mount($id)
    {
        $this->o_id = (int) $id;

        $this->loadOrder();     // load order once
        $this->hydrateUiState(); // set selectedDriver/status filters
        $this->buildDriverList(); // build list + dispatch select2 init
    }

    /** -------------------------
     *  Core loaders
     *  ------------------------- */

    private function loadOrder(): void
    {
        $this->orderData = Order::with([
            'orderItems',
            'orderItems.product.variation.images',
            'customer.customer_profile',
            'customer.wallet',
        ])->find($this->o_id);
    }

    private function hydrateUiState(): void
    {
        if (!$this->orderData) return;

        // status dropdowns
        $this->statusFilter = $this->orderData->status;
        $this->statusPaymentFilter = $this->orderData->payment_status;

        // driver dropdown + driver vehicle info
        $this->selectedDriver = $this->orderData->driver;
        $this->driverDataInit();
    }

    private function refreshUi(): void
    {
        $this->loadOrder();
        $this->hydrateUiState();
        $this->buildDriverList(); // also dispatch select2 init
        $this->emitSelf('$refresh');
    }

    /** -------------------------
     *  Driver UI
     *  ------------------------- */

    public function driverDataInit(): void
    {
        if (!$this->selectedDriver) {
            $this->carModel = 'N/A';
            $this->plateNumber = 'N/A';
            return;
        }

        $driverData = User::with('driver')->find($this->selectedDriver);

        $this->carModel    = $driverData->driver->vehicle_model ?? 'N/A';
        $this->plateNumber = $driverData->driver->plate_number ?? 'N/A';
    }

    public function driverData(): void
    {
        if (!$this->selectedDriver) {
            $this->toast('error', __('Select a driver first'));
            return;
        }

        $driverData = User::with('profile', 'driver')->find($this->selectedDriver);

        if (!$driverData) {
            $this->toast('error', __('Driver not found'));
            return;
        }

        DB::beginTransaction();
        try {
            $order = Order::select('id', 'tracking_number')->find($this->o_id);

            if (!$order) {
                DB::rollBack();
                $this->toast('error', __('Record Not Found'));
                return;
            }

            // update order driver
            Order::where('id', $this->o_id)->update(['driver' => $driverData->id]);

            DB::commit();

            // update UI props (fast)
            $this->carModel    = $driverData->driver->vehicle_model ?? 'N/A';
            $this->plateNumber = $driverData->driver->plate_number ?? 'N/A';

            // notify admins/drivers
            $this->notifyDriverChanged($order, $driverData);

            // broadcast
            try {
                broadcast(new EventDriverUpdated(
                    $order->tracking_number,
                    ($driverData->profile->first_name ?? '') . ' ' . ($driverData->profile->last_name ?? '')
                ))->toOthers();
            } catch (\Throwable $e) {
                $this->toast('info', __('Your Internet is Weak!: ') . $e->getMessage());
            }

            $this->toast('success', __('Driver Updated'));

            // refresh UI state + select2 value
            $this->refreshUi();
        } catch (\Throwable $e) {
            DB::rollBack();
            $this->toast('error', __('U-Error: ') . $e->getMessage());
        }
    }

    private function notifyDriverChanged(Order $order, User $driverData): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Administrator', 'Data Entry Specialist', 'Finance Manager', 'Order Processor']);
        })->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        foreach ($adminUsers as $admin) {
            if (
                !$admin->notifications()
                    ->where('data->order_id', $order->tracking_number)
                    ->where('data->driverId', $driverData->id)
                    ->exists()
            ) {
                $admin->notify(new NotifyDriverUpdate(
                    $order->tracking_number,
                    $order->id,
                    $driverData->id,
                    "Order ID: [#{$order->tracking_number}] has been Transferred to {$driverData->profile->first_name} {$driverData->profile->last_name}"
                ));
            }
        }

        $driverData->notify(new NotifyDriverUpdate(
            $order->tracking_number,
            $order->id,
            $driverData->id,
            "Order ID: [#{$order->tracking_number}] has been Transferred to {$driverData->profile->first_name} {$driverData->profile->last_name}"
        ));
    }

    /** Build driver list + dispatch select2 init */
    private function buildDriverList(): void
    {
        if (!$this->orderData) return;

        $latitude  = $this->orderData->latitude;
        $longitude = $this->orderData->longitude;

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
                "children" => $driversInZone->map(fn($d) => [
                    "id" => $d->id,
                    "driverName" => $d->profile->first_name ?? $d->username
                ])->toArray()
            ],
            [
                "text" => "Drivers outside the Zone",
                "children" => $driversOutOfZone->map(fn($d) => [
                    "id" => $d->id,
                    "driverName" => $d->username
                ])->toArray()
            ]
        ];

        // Select2 sync
        $this->dispatchBrowserEvent('driver-select2:init', [
            'componentId' => $this->id,
            'value' => $this->selectedDriver,
        ]);
    }

    /** -------------------------
     *  Payment status
     *  ------------------------- */

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

        $new = $this->statusPaymentFilter;
        $old = $order->payment_status;

        DB::beginTransaction();
        try {
            if ($new === 'refunded') {
                $this->performRefund($order);
            } else {
                if ($old === 'refunded') {
                    $this->reverseRefund($order);
                }

                $order->payment_status = $new;
                $order->save();
            }

            DB::commit();

            $this->notifyPaymentStatus($order);

            try {
                broadcast(new EventOrderPaymentStatusUpdated($order->tracking_number, $order->payment_status))->toOthers();
            } catch (\Throwable $e) {
                $this->toast('info', __('Your Internet is Weak!: ') . $e->getMessage());
            }

            $this->toast('success', __('Status Updated Successfully'));
            $this->refreshUi();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin Payment Status Update Error: ' . $e->getMessage(), ['order_id' => $order->id ?? null]);
            $this->toast('error', __('Update failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    /** -------------------------
     *  Shipping status
     *  ------------------------- */

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

        $new = $this->statusFilter;
        $old = $order->status;

        DB::beginTransaction();
        try {
            if ($new === 'refunded') {
                $this->performRefund($order);
            } else {
                if ($old === 'refunded' || $order->payment_status === 'refunded') {
                    $this->reverseRefund($order);
                }

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
            $this->refreshUi();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('Admin Shipping Status Update Error: ' . $e->getMessage(), ['order_id' => $order->id ?? null]);
            $this->toast('error', __('Update failed: :msg', ['msg' => $e->getMessage()]));
        }
    }

    /** -------------------------
     *  Refund core
     *  ------------------------- */

    private function performRefund(Order $order): void
    {
        $itemsSubtotal = (int) $order->orderItems->sum('total_iqd');
        $shipping      = (int) ($order->shipping_amount ?? 0);
        $refundableBase = $itemsSubtotal + $shipping;

        $alreadyRefunded = (int) ($order->refunded_minor ?? 0);
        $paid            = (int) ($order->paid_minor ?? 0);
        $maxRefundable   = max(0, $paid - $alreadyRefunded);
        $toRefund        = max(0, min($refundableBase, $maxRefundable));

        if ($toRefund <= 0) {
            throw new \RuntimeException(__('Nothing to refund'));
        }

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

        $order->refunded_minor = $alreadyRefunded + $toRefund;
        $order->payment_status = 'refunded';
        if ($order->status !== 'delivered') {
            $order->status = 'refunded';
        }
        $order->save();

        $payment = Payment::create([
            'order_id'            => $order->id,
            'amount_minor'        => $toRefund,
            'currency'            => 'IQD',
            'method'              => 'Wallet',
            'status'              => 'successful',
            'provider'            => 'Wallet',
            'provider_payment_id' => null,
            'idempotency_key'     => Str::uuid(),
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

    private function reverseRefund(Order $order): void
    {
        $itemsSubtotal = (int) $order->orderItems->sum('total_iqd');
        $shipping      = (int) ($order->shipping_amount ?? 0);
        $refunded      = (int) ($order->refunded_minor ?? 0);

        $maxReversible = min($refunded, $itemsSubtotal + $shipping);
        if ($maxReversible <= 0) return;

        $wallet = $order->customer->wallet()->lockForUpdate()->firstOrCreate(['currency' => 'IQD']);

        if ($wallet->balance_minor < $maxReversible) {
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
        $order->save();

        $payment = Payment::create([
            'order_id'            => $order->id,
            'amount_minor'        => $maxReversible,
            'currency'            => 'IQD',
            'method'              => 'Wallet',
            'status'              => 'successful',
            'provider'            => 'Wallet',
            'provider_payment_id' => null,
            'idempotency_key'     => Str::uuid(),
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
     *  Notify helpers
     *  ------------------------- */

    private function notifyPaymentStatus(Order $order): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Administrator', 'Data Entry Specialist', 'Finance Manager', 'Order Processor']);
        })->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        foreach ($adminUsers as $admin) {
            if (
                !$admin->notifications()
                    ->where('data->order_id', $order->tracking_number)
                    ->where('data->status', $order->payment_status)
                    ->exists()
            ) {
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
            $driverUser?->notify(new NotifyOrderPaymentStatusChanged(
                $order->tracking_number,
                $order->id,
                $order->payment_status,
                "Order ID [#{$order->tracking_number}] Payment has been updated to {$order->payment_status}!"
            ));
        }
    }

    private function notifyShippingStatus(Order $order): void
    {
        $adminUsers = User::whereHas('roles', function ($query) {
            $query->whereIn('name', ['Administrator', 'Data Entry Specialist', 'Finance Manager', 'Order Processor']);
        })->whereDoesntHave('roles', function ($query) {
            $query->where('name', 'Driver');
        })->get();

        foreach ($adminUsers as $admin) {
            if (
                !$admin->notifications()
                    ->where('data->order_id', $order->tracking_number)
                    ->where('data->status', $order->status)
                    ->exists()
            ) {
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
            $driverUser?->notify(new NotifyOrderStatusChanged(
                $order->tracking_number,
                $order->id,
                $order->status,
                "Order ID {$order->tracking_number} has been updated to {$order->status}"
            ));
        }
    }

    /** -------------------------
     *  Geometry
     *  ------------------------- */

    private function isWithinZone($zoneCoordinates, $latitude, $longitude): bool
    {
        $coordinates = json_decode($zoneCoordinates, true);

        if (!is_array($coordinates) || count($coordinates) < 3) return false;

        foreach ($coordinates as $p) {
            if (!isset($p['lat'], $p['lng'])) return false;
        }

        return $this->isCoordinateInsidePolygon($coordinates, $latitude, $longitude);
    }

    private function isCoordinateInsidePolygon(array $coordinates, $lat, $lng): bool
    {
        $inside = false;
        $num = count($coordinates);

        for ($i = 0, $j = $num - 1; $i < $num; $j = $i++) {
            $v1 = $coordinates[$i];
            $v2 = $coordinates[$j];

            if (($v1['lat'] > $lat) != ($v2['lat'] > $lat) &&
                ($lng < ($v2['lng'] - $v1['lng']) * ($lat - $v1['lat']) / ($v2['lat'] - $v1['lat']) + $v1['lng'])) {
                $inside = !$inside;
            }
        }

        return $inside;
    }

    /** -------------------------
     *  Broadcast reload handlers
     *  ------------------------- */

    public function driverReload(): void
    {
        // only refresh UI (do NOT update DB)
        $this->refreshUi();
        $this->emit('notificationSound');
    }

    public function statusReload(): void
    {
        $this->refreshUi();
        $this->emit('notificationSound');
    }

    public function paymentReload(): void
    {
        $this->refreshUi();
        $this->emit('notificationSound');
    }

    /** -------------------------
     *  UI helpers
     *  ------------------------- */

    private function toast($type, $message): void
    {
        $this->dispatchBrowserEvent('alert', ['type' => $type, 'message' => $message]);
    }

    public function render()
    {
        // no DB here now — use cached orderData
        $order = $this->orderData;

        $sum = $order ? (int) $order->orderItems->sum('total_iqd') : 0;

        return view('super-admins.pages.orderviewer.order-viewer', [
            'orderData' => $order,
            'subTotal'  => $sum,
        ]);
    }
}
