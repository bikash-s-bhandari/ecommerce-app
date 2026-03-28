<?php

namespace Modules\Order\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Events\OrderShipped;
use Modules\Order\Listeners\SendOrderPlacedEmail;
use Modules\Order\Listeners\SendOrderShippedEmail;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        OrderPlaced::class => [
            SendOrderPlacedEmail::class,
        ],
        OrderShipped::class => [
            SendOrderShippedEmail::class,
        ],
    ];

    protected static $shouldDiscoverEvents = false;

    protected function configureEmailVerification(): void {}
}
