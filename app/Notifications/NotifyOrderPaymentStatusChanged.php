<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class NotifyOrderPaymentStatusChanged extends Notification
{
    use Queueable;

    protected $trackingNumber;
    protected $orderNumber;
    protected $status;
    protected $message;

    public function __construct($trackingNumber, $orderNumber,$status, $message)
    {
        $this->trackingNumber = $trackingNumber;
        $this->orderNumber = $orderNumber;
        $this->status = $status;
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
            'status' => $this->status,
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
            'status' => $this->status,
            'message' => $this->message,
        ];
    }
}
