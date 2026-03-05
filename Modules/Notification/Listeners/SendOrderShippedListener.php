<?php

namespace Modules\Notification\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Notification\Mail\OrderShippedMail;
use Modules\Order\Events\OrderShipped;

class SendOrderShippedListener implements ShouldQueue
{
    public string $queue = 'notifications';

    public function handle(OrderShipped $event): void
    {
        Mail::to($event->order->user->email)->send(new OrderShippedMail($event->order));
    }
}
