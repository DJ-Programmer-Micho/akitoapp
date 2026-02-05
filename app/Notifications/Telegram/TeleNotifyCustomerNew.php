<?php

namespace App\Notifications\Telegram;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TeleNotifyCustomerNew extends Notification
{
    protected $o_id;
    protected $full_name;
    protected $phone;
    protected $businessModule;
    protected $businessName;
    protected $telegram_channel_link;

    public function __construct($id, $full_name, $phone, $businessModule, $businessName)
    {
        $this->o_id = $id;
        $this->full_name = $full_name;
        $this->phone = $phone;
        $this->businessModule = $businessModule;
        $this->businessName = $businessName;

        // dd(
        //     $this->tracking_number,
        //     $this->full_name,
        //     $this->phone,
        //     $this->order_item,
        //     $this->shipping_cost,
        //     $this->total_cost,

        // );
    }

    public function via($notifiable)
    {
        return [TelegramChannel::class];
    }

    public function toTelegram($notifiable)
    {

        $customer_url = env('APP_URL_LOCALE') . '/super-admin/customer-profile/' . $this->o_id;
        // $customer_url = 'http://127.0.0.1:8000/en' . '/super-admin/customer-profile/' . $this->o_id;
        $registrationId = '#'.$this->o_id;
        // $registration3Id = $this->tracking_number;

        $content = "*" . 'NEW CUSTOMER' . "*\n"
        . "*" . '-----------------' . "*\n" 
        . "*" . 'Customer_ID: ' . $registrationId . "*\n"
        . "*" . 'Name: ' . $this->full_name . "*\n"
        . "*" . 'Phone: ' . $this->phone . "*\n"
        . "*" . 'Business Module: ' . $this->businessModule . "*\n";

        if($this->businessModule != 'Personal') {
            $content .= "*" . 'Business Name: ' . $this->businessName . "*\n";
        }

       return TelegramMessage::create()
        ->to(env('TELEGRAM_BOT_CUSTOMER_GROUP_ID') ?? '-1003782684239')
        ->content($content)
        ->button('Customer View', $customer_url);
    }

    public function toArray($notifiable)
    {

    }
}
