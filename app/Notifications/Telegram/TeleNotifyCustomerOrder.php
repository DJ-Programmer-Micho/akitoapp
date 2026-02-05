<?php

namespace App\Notifications\Telegram;

use Illuminate\Notifications\Notification;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Telegram\TelegramMessage;

class TeleNotifyCustomerOrder extends Notification
{
    protected $o_id;
    protected $tracking_number;
    protected $full_name;
    protected $phone;
    protected $order_item;
    protected $shipping_cost;
    protected $total_cost;
    protected $telegram_channel_link;

    public function __construct($id, $tracking_number, $full_name, $phone, $order_item, $shipping_cost, $total_cost)
    {
        $this->o_id = $id;
        $this->tracking_number = $tracking_number;
        $this->full_name = $full_name;
        $this->phone = $phone;
        $this->order_item = $order_item;
        $this->shipping_cost = $shipping_cost;
        $this->total_cost = $total_cost;

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
        $order_url = env('APP_URL_LOCALE') . '/super-admin/order-management-viewer/' . $this->o_id;

        $registrationId = '#' . $this->tracking_number;

        $content = "*NEW ORDER*\n"
            . "*-----------------*\n"
            . "*Order: {$registrationId}*\n"
            . "*Name: {$this->full_name}*\n"
            . "*Phone: {$this->phone}*\n"
            . "*-----------------*\n"
            . "*Cart Items*\n"
            . "*-----------------*\n";

        foreach ($this->order_item as $index => $item) {
            $productName = $item->product->productTranslation[0]->name ?? 'Unknown Product';
            $content .= "*" . ($index + 1) . "- " . $productName . "*\n";
        }

        $content .= "*-----------------*\n"
            . "*Shipping Cost: " . number_format($this->shipping_cost, 0) . " IQD*\n"
            . "*Sub Total: " . number_format(($this->total_cost - $this->shipping_cost), 0) . " IQD*\n"
            . "*Grand Total: " . number_format($this->total_cost, 0) . " IQD*\n";

        return TelegramMessage::create()
            ->to(env('TELEGRAM_BOT_ORDER_GROUP_ID'))
            ->content($content)
            ->button('Order View', $order_url);
    }


    public function toArray($notifiable)
    {

    }
}
