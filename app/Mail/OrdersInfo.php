<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Restaurant;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrdersInfo extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The order instance.
     *
     * @var \App\Models\Order
     */
    public $order;
    public $orders;

    /**
     * The order instance.
     *
    * @var \App\Models\Restaurant
     */
    public $restaurant;

    public function __construct($userName, $restaurant, $numberOfProducts, $orders, $order,array $priceData,array $nameData)
    {
        $this->userName = $userName;
        $this->restaurant = $restaurant;
        $this->numberOfProducts = $numberOfProducts;
        $this->orders = $orders;
        $this->order = $order;
        $this->priceData = $priceData;
        $this->nameData = $nameData;
    }

    /**
     * Get the message envelope.
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Orders Info',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            markdown: 'emails.orders',
            view: 'emails.orders',
            with: [
                'userName' => $this->userName ,
                'restaurantName' => $this->restaurant->store_name,
                'numberOfProducts' => $this->numberOfProducts,
                'orders' => $this->orders,
                'deliveryFee' => $this->orders->delivery_fee,
                'paymentType' => $this->orders->payment_type,
                'additionRequest' => $this->orders->addition_request,
                'nameData' => $this->nameData,
                'priceData' => $this->priceData,
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [];
    }
}
