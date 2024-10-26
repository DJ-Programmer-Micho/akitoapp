<?php

namespace App\Http\Livewire\SuperAdmins;

use App\Models\User;
use App\Models\Zone;
use App\Models\Order;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Events\EventDriverUpdated;
use App\Events\EventOrderStatusUpdated;
use App\Events\EventOrderPaymentStatusUpdated;
use App\Notifications\NotifyDriverUpdate;
use App\Notifications\NotifyOrderStatusChanged;
use App\Notifications\NotifyOrderPaymentStatusChanged;

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

    public function mount($id)
    {
        $this->o_id = $id;
        $this->getList();
    }

    protected $listeners = [
        'echo:AdminChannel,EventOrderStatusUpdated' => 'statusReload',
        'echo:AdminChannel,EventDriverUpdated' => 'driverReload',
        'echo:AdminChannel,EventOrderPaymentStatusUpdated' => 'paymentReload',
    ];
    
    
    public function updatePaymentStatus(int $id)
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
                $adminUsers = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Administrator')
                          ->orWhere('name', 'Data Entry Specialist')
                          ->orWhere('name', 'Finance Manager')
                          ->orWhere('name', 'Order Processor');
                })->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Driver');
                })->get();
    
                foreach ($adminUsers as $admin) {
                    if (!$admin->notifications()->where('data->order_id', $order->tracking_number)
                        ->where('data->status', $order->status)->exists()) {
                        $admin->notify(new NotifyOrderPaymentStatusChanged(
                            $order->tracking_number,
                            $order->id, 
                            $order->status, 
                            "Order ID {$order->tracking_number} Payment has been updated to {$order->payment_status}", 
                        ));
                    }
                }
    
                // Notify specific driver
                if($this->selectedDriver) {
                    $driverUser = User::find($this->selectedDriver);
                    $driverUser->notify(new NotifyOrderPaymentStatusChanged(
                        $order->tracking_number,
                        $order->id, 
                        $order->status,
                        "Order ID {$order->tracking_number} Payment has been updated to {$order->payment_status}!",
                    ));
                }
    
                // Broadcast to admins and the specific driver
                try {
                    broadcast(new EventOrderPaymentStatusUpdated($order->tracking_number, $order->payment_status))->toOthers();    
                } catch (\Exception $e) {
                    $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('Your Internet is Weak!: ' . $e->getMessage())]);
                    return;
                }
            }

        } else {
            // Dispatch an error message if the order is not found
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }
    
    public function updateStatus(int $id)
    {
        // Find the order by ID
        $order = Order::find($id);
    
        if ($order) {
            // Ensure that the selected payment status is not empty
            if (!empty($this->statusFilter)) {
                // Update the payment status with the selected filter value
                $order->status = $this->statusFilter;
                $order->save();
    
                // Dispatch a success message
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'success',
                    'message' => __('Status Updated Successfully')
                ]);
                
                $adminUsers = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Administrator')
                          ->orWhere('name', 'Data Entry Specialist')
                          ->orWhere('name', 'Finance Manager')
                          ->orWhere('name', 'Order Processor');
                })->whereDoesntHave('roles', function ($query) {
                    $query->where('name', 'Driver');
                })->get();
    
                foreach ($adminUsers as $admin) {
                    if (!$admin->notifications()->where('data->order_id', $order->tracking_number)
                        ->where('data->status', $order->status)->exists()) {
                        $admin->notify(new NotifyOrderStatusChanged(
                            $order->tracking_number, 
                            $order->id, 
                            $order->status, 
                            "Order ID {$order->tracking_number} has been updated to {$order->status}", 
                        ));
                    }
                }
    
                // Notify specific driver
                if($this->selectedDriver) {
                    $driverUser = User::find($this->selectedDriver);
                    $driverUser->notify(new NotifyOrderStatusChanged(
                        $order->tracking_number,
                        $order->id, 
                        $order->status,
                        "Order ID {$order->tracking_number} has been updated to {$order->status}",
                    ));
                }
    
                // Broadcast to admins and the specific driver
                try {
                    broadcast(new EventOrderStatusUpdated($order->tracking_number, $order->status))->toOthers();    
                } catch (\Exception $e) {
                    $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('Your Internet is Weak!: ' . $e->getMessage())]);
                    return;
                }
    
            } else {
                $this->dispatchBrowserEvent('alert', [
                    'type' => 'error',
                    'message' => __('Order Data Not Found')
                ]);
            }
        } else {
            // Dispatch an error message if the order is not found
            $this->dispatchBrowserEvent('alert', [
                'type' => 'error',
                'message' => __('Record Not Found')
            ]);
        }
    }
    


    public function driverDataInit(){ 
        $driverData = user::where('id', $this->selectedDriver)->first();
        $this->carModel = $driverData->driver->vehicle_model ?? 'N/A';
        $this->plateNumber = $driverData->driver->plate_number ?? 'N/A';
    }
    public function driverData(){
        $driverData = user::where('id', $this->selectedDriver)->first();
        $this->carModel = $driverData->driver->vehicle_model ?? 'N/A';
        $this->plateNumber = $driverData->driver->plate_number ?? 'N/A';

        try {
            $order = Order::where('id', $this->o_id)->first(['id','tracking_number']);

            Order::where('id', $this->o_id)->update([
                'driver' => $driverData->id,
            ]);
            //SEND NOTIFICATION
            $adminUsers = User::whereHas('roles', function ($query) {
                $query->where('name', 'Administrator')
                      ->orWhere('name', 'Data Entry Specialist')
                      ->orWhere('name', 'Finance Manager')
                      ->orWhere('name', 'Order Processor');
            })->whereDoesntHave('roles', function ($query) {
                $query->where('name', 'Driver');
            })->get();

            foreach ($adminUsers as $admin) {
                if (!$admin->notifications()->where('data->order_id', $order->tracking_number)
                    ->where('data->driverId', $driverData->id)->exists()) {
                    $admin->notify(new NotifyDriverUpdate(
                        $order->tracking_number, 
                        $order->id, 
                        $driverData->id, 
                        "Order ID: [#{$order->tracking_number}] has been Transferred to {$driverData->profile->first_name} {$driverData->profile->last_name}", 
                    ));
                }
            }

            // Notify specific driver
            if($this->selectedDriver) {
                $driverUser = User::find($this->selectedDriver);
                $driverUser->notify(new NotifyDriverUpdate(
                    $order->tracking_number, 
                    $order->id, 
                    $driverData->id, 
                    "Order ID: [#{$order->tracking_number}] has been Transferred to {$driverData->profile->first_name} {$driverData->profile->last_name}", 
                ));
            }

            // Broadcast to admins and the specific driver
            try {
                broadcast(new EventDriverUpdated($order->tracking_number, $driverData->profile->first_name .' '. $driverData->profile->last_name))->toOthers();    
            } catch (\Exception $e) {
                $this->dispatchBrowserEvent('alert', ['type' => 'info', 'message' => __('Your Internet is Weak!: ' . $e->getMessage())]);
                return;
            }
            $this->dispatchBrowserEvent('alert', ['type' => 'success', 'message' => __('Driver Updated')]);
        } catch (\Exception $e) {
            $this->dispatchBrowserEvent('alert', ['type' => 'error', 'message' => __('U-Error: ' . $e->getMessage())]);
        }
    }
    
    private function getList()
{
    // Step 1: Fetch the order location
    $orderLocation = Order::where('id', $this->o_id)->first(['latitude', 'longitude','driver']);
    
    if (!$orderLocation) {
        return []; // Handle error
    }
    if ($orderLocation->driver) {
        $this->selectedDriver = $orderLocation->driver;
        $this->driverDataInit();
    }
    
    $latitude = $orderLocation->latitude;
    $longitude = $orderLocation->longitude;

    // Step 2: Fetch zones that match the order location coordinates
    $zones = Zone::where('status', 1)->get()
        ->filter(function ($zone) use ($latitude, $longitude) {
            return $this->isWithinZone($zone->coordinates, $latitude, $longitude);
        });

    // Step 3: Collect driver teams within the matched zones
    $teamsInZone = $zones->pluck('delivery_team')->toArray();

    // Step 4: Fetch users who are drivers (role ID 8) and in the zone
    $driversInZone = User::whereHas('roles', function($query) {
        $query->where('roles.id', 8);  // Specify 'roles.id' to avoid ambiguity
    })
    ->whereHas('driverTeam', function($query) use ($teamsInZone) {
        $query->whereIn('driver_teams.id', $teamsInZone);
    })->get();

    // Step 5: Fetch users who are drivers (role ID 8) but not in the zone
    $driversOutOfZone = User::whereHas('roles', function($query) {
        $query->where('roles.id', 8);  // Specify 'roles.id' to avoid ambiguity
    })
    ->whereDoesntHave('driverTeam', function($query) use ($teamsInZone) {
        $query->whereIn('driver_teams.id', $teamsInZone);
    })->get();

    // Step 6: Format the data for Select2
    $this->driverList = [
        [
            "text" => "Drivers in the Zone",
            "children" => $driversInZone->map(function($driver) {
                return [
                    "id" => $driver->id,
                    "driverName" => $driver->profile->first_name // Assuming 'username' is used for driver names
                ];
            })->toArray()
        ],
        [
            "text" => "Drivers outside the Zone",
            "children" => $driversOutOfZone->map(function($driver) {
                return [
                    "id" => $driver->id,
                    "driverName" => $driver->username
                ];
            })->toArray()
        ]
    ];
}
    
    
    
private function isWithinZone($zoneCoordinates, $latitude, $longitude)
{
    // Log the raw coordinates for debugging
    Log::info('Raw coordinates: ', ['zoneCoordinates' => $zoneCoordinates]);

    // Decode the coordinates since they are stored as a JSON string in the database
    $coordinates = json_decode($zoneCoordinates, true);

    // Check for decoding errors
    if (json_last_error() !== JSON_ERROR_NONE) {
        Log::error('JSON decode error: ' . json_last_error_msg());
        throw new \Exception("Invalid coordinates format");
    }

    // Check if the coordinates array is valid and countable
    if (!is_array($coordinates) || count($coordinates) < 3) {
        throw new \Exception("Invalid coordinates format");
    }

    // Loop through the coordinates to check if the order is within the zone
    foreach ($coordinates as $coordinate) {
        if (!isset($coordinate['lat'], $coordinate['lng'])) {
            throw new \Exception("Coordinate must contain 'lat' and 'lng' keys");
        }

        // Check if the order's latitude and longitude are within the bounds of the zone's coordinates
        if ($this->isCoordinateInsidePolygon($coordinates, $latitude, $longitude)) {
            return true;
        }
    }

    return false;
}


    
    
private function isCoordinateInsidePolygon($coordinates, $lat, $lng)
{
    $inside = false;
    $numCoordinates = count($coordinates);

    // Loop through each vertex of the polygon
    for ($i = 0, $j = $numCoordinates - 1; $i < $numCoordinates; $j = $i++) {
        $vertex1 = $coordinates[$i];
        $vertex2 = $coordinates[$j];

        // Ensure that vertex1 and vertex2 are valid points
        if (!isset($vertex1['lat'], $vertex1['lng']) || !isset($vertex2['lat'], $vertex2['lng'])) {
            throw new \Exception("Invalid coordinate points in polygon");
        }

        // Check if the point is within the y-range of the polygon edge
        if (($vertex1['lat'] > $lat) != ($vertex2['lat'] > $lat) &&
            ($lng < ($vertex2['lng'] - $vertex1['lng']) * ($lat - $vertex1['lat']) / ($vertex2['lat'] - $vertex1['lat']) + $vertex1['lng'])) {
            $inside = !$inside; // Toggle the inside flag
        }
    }

    return $inside;
}


    

    public function render()
    {
        $sum = 0;
        $order = Order::with('orderItems','orderItems.product.variation.images', 'customer.customer_profile')->find($this->o_id);
        // Return view with data
        // dd($order->orderItems);
        foreach($order->orderItems as $item) {
            $sum = $sum + $item->total;
        }
        
        return view('super-admins.pages.orderviewer.order-viewer', [
            'orderData' => $order,
            'subTotal' => $sum,
        ]);
    }

    public function driverReload() {
        $orderLocation = Order::where('id', $this->o_id)->first(['latitude', 'longitude','driver']);
    
        if (!$orderLocation) {
            return []; // Handle error
        }
        if ($orderLocation->driver) {
            $this->selectedDriver = $orderLocation->driver;
            $this->driverData();
        }
        $this->emit('notificationSound');
    }
    public function statusReload() {
        $orderLocation = Order::where('id', $this->o_id)->first(['status']);
    
        if (!$orderLocation) {
            return []; // Handle error
        }
        if ($orderLocation->status) {
            $this->statusFilter = $orderLocation->status;
        }
        $this->emit('notificationSound');
    }
    public function paymentReload() {
        $orderLocation = Order::where('id', $this->o_id)->first(['payment_status']);
    
        if (!$orderLocation) {
            return []; // Handle error
        }
        if ($orderLocation->payment_status) {
            $this->statusPaymentFilter = $orderLocation->payment_status;
        }
        $this->emit('notificationSound');
    }
}