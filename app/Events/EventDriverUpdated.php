<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class EventDriverUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $trackingNumber;
    public $driverName;

    public function __construct($trackingNumber, $driverName)
    {
        $this->trackingNumber = $trackingNumber;
        $this->driverName = $driverName;
    }


    public function broadcastOn(): array
    {
        $channels = []; // Initialize an empty array
        $channels[] = new Channel('AdminChannel'); // Always include the orders channel
    
        return $channels; // Return the array of channels
    }

    public function broadcastWith()
    {
        return [
            'tracking_number' => $this->trackingNumber,
            'driverName' => $this->driverName,
        ];
    }
}
