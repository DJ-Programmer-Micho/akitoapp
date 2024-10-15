<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NotifyDriverUpdate extends Notification
{
    use Queueable;

    protected $trackingNumber;
    protected $orderNumber;
    protected $driverId;
    protected $message;

    public function __construct($trackingNumber, $orderNumber, $driverId, $message)
    {
        $this->trackingNumber = $trackingNumber;
        $this->orderNumber = $orderNumber;
        $this->driverId = $driverId;
        $this->message = $message;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    // public function via(object $notifiable): array
    public function via($notifiable)
    {
        return ['database','broadcast'];
    }

    /**
     * Get the mail representation of the notification.
     */

    public function toDatabase($notifiable)
    {
        return new BroadcastMessage([
            'tracking_number' => $this->trackingNumber,
            'orderNumber' => $this->orderNumber,
            'driverId' => $this->driverId,
            'message' => $this->message,
        ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'tracking_number' => $this->trackingNumber,
            'orderNumber' => $this->orderNumber,
            'driverId' => $this->driverId,
            'message' => $this->message,
        ];
    }
}
