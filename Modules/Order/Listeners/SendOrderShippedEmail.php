<?php

namespace Modules\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Events\OrderShipped;
use Modules\Order\Mail\OrderShippedMail;

class SendOrderShippedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderShipped $event): void
    {
        $order = $event->order->loadMissing(['items', 'user']);

        Mail::to($order->user->email)->send(new OrderShippedMail($order));
    }
}
