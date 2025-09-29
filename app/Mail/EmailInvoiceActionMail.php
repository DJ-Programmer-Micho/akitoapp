<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailInvoiceActionMail extends Mailable
{
    use Queueable, SerializesModels;
    public $orderData, $subTotal;
    /**
     * Create a new message instance.
     */
    public function __construct($order)
    {
        $this->orderData = $order;
        $this->subTotal = $order->total_amount_iqd;
    }
    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "Italiancoffee-co - Order ID #{$this->orderData->tracking_number}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'super-admins.pdf.orderinvoice.order-invoice-action-print',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
