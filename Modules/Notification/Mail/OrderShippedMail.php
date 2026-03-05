<?php

namespace Modules\Notification\Mail;

class OrderShippedMail extends BaseOrderMail
{
    protected function getSubject(): string
    {
        return 'Your Order Has Shipped — ' . $this->order->order_number;
    }
    protected function getBody(): string
    {
        return "Great news! Your order #{$this->order->order_number} has been shipped and is on its way to you.";
    }
    protected function getActionText(): string
    {
        return 'Track Order';
    }
    protected function getActionUrl(): string
    {
        return config('app.frontend_url') . '/orders/' . $this->order->id;
    }
}
