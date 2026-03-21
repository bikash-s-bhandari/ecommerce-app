<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Admin\Queries\OrderStatsQuery;
use Modules\Auth\Models\User;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;

uses(RefreshDatabase::class);

function makeOrder(array $overrides = []): Order
{
    $user = User::factory()->create();

    return Order::create(array_merge([
        'user_id'          => $user->id,
        'shipping_address' => ['street' => '123 Main St', 'city' => 'Kathmandu'],
        'status'           => OrderStatusEnum::PENDING,
        'subtotal'         => 100.00,
        'tax'              => 10.00,
        'shipping_fee'     => 5.00,
        'total'            => 115.00,
    ], $overrides));
}

it('counts total orders', function () {
    makeOrder();
    makeOrder();
    makeOrder();

    $stats = (new OrderStatsQuery)->get();

    expect((int) $stats->total_orders)->toBe(3);
});

it('counts orders by status', function () {
    makeOrder(['status' => OrderStatusEnum::PENDING]);
    makeOrder(['status' => OrderStatusEnum::PENDING]);
    makeOrder(['status' => OrderStatusEnum::PROCESSING]);
    makeOrder(['status' => OrderStatusEnum::SHIPPED]);

    $stats = (new OrderStatsQuery)->get();

    expect((int) $stats->pending_orders)->toBe(2)
        ->and((int) $stats->processing_orders)->toBe(1)
        ->and((int) $stats->shipped_orders)->toBe(1);
});

it('calculates total revenue excluding cancelled orders', function () {
    makeOrder(['total' => 100.00, 'status' => OrderStatusEnum::PENDING]);
    makeOrder(['total' => 200.00, 'status' => OrderStatusEnum::DELIVERED]);
    makeOrder(['total' => 999.00, 'status' => OrderStatusEnum::CANCELLED]);

    $stats = (new OrderStatsQuery)->get();

    expect((float) $stats->total_revenue)->toBe(300.00);
});

it('calculates today revenue excluding cancelled orders', function () {
    makeOrder(['total' => 50.00, 'status' => OrderStatusEnum::PENDING]);
    makeOrder(['total' => 999.00, 'status' => OrderStatusEnum::CANCELLED]);

    $stats = (new OrderStatsQuery)->get();

    expect((float) $stats->today_revenue)->toBe(50.00);
});

it('calculates week revenue excluding cancelled orders', function () {
    makeOrder(['total' => 80.00, 'status' => OrderStatusEnum::CONFIRMED]);
    makeOrder(['total' => 20.00, 'status' => OrderStatusEnum::SHIPPED]);
    makeOrder(['total' => 500.00, 'status' => OrderStatusEnum::CANCELLED]);

    $stats = (new OrderStatsQuery)->get();

    expect((float) $stats->week_revenue)->toBe(100.00);
});

it('returns zero totals when no orders exist', function () {
    $stats = (new OrderStatsQuery)->get();

    expect((int) $stats->total_orders)->toBe(0)
        ->and((float) ($stats->total_revenue ?? 0))->toBe(0.0)
        ->and((int) $stats->pending_orders)->toBe(0);
});
