<?php

namespace Modules\Notification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Notification\Mail\OrderConfirmedMail;
use Modules\Order\Events\OrderPlaced;

class SendOrderConfirmationListener implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(OrderPlaced $event): void
    {
        Mail::to($event->order->user->email)->send(new OrderConfirmedMail($event->order));
    }
}
