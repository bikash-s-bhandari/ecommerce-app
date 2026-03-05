<?php

namespace Modules\Notification\Mail;

class OrderConfirmedMail extends BaseOrderMail
{
    protected function getSubject(): string
    {
        return 'Order Confirmed — ' . $this->order->order_number;
    }
    protected function getBody(): string
    {
        return "Your order #{$this->order->order_number} has been confirmed and is being prepared. Total: \${$this->order->total}";
    }
    protected function getActionText(): string
    {
        return 'View Order';
    }
    protected function getActionUrl(): string
    {
        return config('app.frontend_url') . '/orders/' . $this->order->id;
    }
}
