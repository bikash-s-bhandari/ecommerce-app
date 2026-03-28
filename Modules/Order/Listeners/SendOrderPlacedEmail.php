<?php

namespace Modules\Order\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Mail\OrderPlacedMail;

class SendOrderPlacedEmail implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(OrderPlaced $event): void
    {
        $order = $event->order->loadMissing(['items', 'user', 'payment']);

        Mail::to($order->user->email)->send(new OrderPlacedMail($order));
    }
}
