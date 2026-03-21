<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Admin\Queries\AdminOrderIndexQuery;
use Modules\Auth\Models\User;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;

uses(RefreshDatabase::class);

function seedOrder(array $overrides = []): Order
{
    $user      = User::factory()->create();
    $createdAt = $overrides['created_at'] ?? null;
    unset($overrides['created_at']);

    $order = Order::create(array_merge([
        'user_id'          => $user->id,
        'shipping_address' => ['street' => '123 Main St', 'city' => 'Kathmandu'],
        'status'           => OrderStatusEnum::PENDING,
        'subtotal'         => 100.00,
        'tax'              => 10.00,
        'shipping_fee'     => 5.00,
        'total'            => 115.00,
    ], $overrides));

    if ($createdAt !== null) {
        $order->forceFill(['created_at' => $createdAt])->saveQuietly();
    }

    return $order->fresh();
}

it('paginates all orders when no filters are applied', function () {
    seedOrder();
    seedOrder();
    seedOrder();

    $result = (new AdminOrderIndexQuery(perPage: 10))->paginate();

    expect($result->total())->toBe(3);
});

it('filters orders by status', function () {
    seedOrder(['status' => OrderStatusEnum::PENDING]);
    seedOrder(['status' => OrderStatusEnum::PENDING]);
    seedOrder(['status' => OrderStatusEnum::SHIPPED]);

    $result = (new AdminOrderIndexQuery(status: OrderStatusEnum::PENDING->value))->paginate();

    expect($result->total())->toBe(2);
    $result->each(fn ($o) => expect($o->status->value)->toBe(OrderStatusEnum::PENDING->value));
});

it('filters orders by order_number search', function () {
    $target = seedOrder();
    seedOrder();
    seedOrder();

    $result = (new AdminOrderIndexQuery(search: $target->order_number))->paginate();

    expect($result->total())->toBe(1)
        ->and($result->first()->id)->toBe($target->id);
});

it('filters orders by user email search', function () {
    $user   = User::factory()->create(['email' => 'jane@example.com']);
    $target = Order::create([
        'user_id'          => $user->id,
        'shipping_address' => ['street' => 'X'],
        'status'           => OrderStatusEnum::PENDING,
        'subtotal'         => 50.00,
        'tax'              => 5.00,
        'shipping_fee'     => 2.00,
        'total'            => 57.00,
    ]);
    seedOrder();
    seedOrder();

    $result = (new AdminOrderIndexQuery(search: 'jane@example.com'))->paginate();

    expect($result->total())->toBe(1)
        ->and($result->first()->id)->toBe($target->id);
});

it('returns orders sorted by latest first', function () {
    $older  = seedOrder(['created_at' => now()->subDays(2)]);
    $newer  = seedOrder(['created_at' => now()->subDay()]);
    $newest = seedOrder(['created_at' => now()]);

    $result = (new AdminOrderIndexQuery)->paginate();

    expect($result->items()[0]->id)->toBe($newest->id)
        ->and($result->items()[1]->id)->toBe($newer->id)
        ->and($result->items()[2]->id)->toBe($older->id);
});

it('respects per_page limit', function () {
    foreach (range(1, 15) as $_) {
        seedOrder();
    }

    $result = (new AdminOrderIndexQuery(perPage: 5))->paginate();

    expect($result->perPage())->toBe(5)
        ->and(count($result->items()))->toBe(5)
        ->and($result->total())->toBe(15);
});

it('eager loads user, items, and payment relations', function () {
    seedOrder();

    $result = (new AdminOrderIndexQuery)->paginate();

    $order = $result->first();
    expect($order->relationLoaded('user'))->toBeTrue()
        ->and($order->relationLoaded('items'))->toBeTrue()
        ->and($order->relationLoaded('payment'))->toBeTrue();
});

it('returns empty paginator when no orders match', function () {
    seedOrder(['status' => OrderStatusEnum::PENDING]);

    $result = (new AdminOrderIndexQuery(status: OrderStatusEnum::DELIVERED->value))->paginate();

    expect($result->total())->toBe(0);
});
