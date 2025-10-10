<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class OrderInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Invoice Order #' . $this->order->order_code)
            ->markdown('emails.invoice')
            ->with([
                'order' => $this->order,
                'user' => $this->order->user,
                'items' => $this->order->items,
            ]);
    }
}
