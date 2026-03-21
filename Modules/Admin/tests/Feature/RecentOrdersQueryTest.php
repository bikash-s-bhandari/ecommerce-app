<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Admin\Queries\RecentOrdersQuery;
use Modules\Auth\Models\User;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;

uses(RefreshDatabase::class);

function createOrderForUser(?User $user = null, array $overrides = []): Order
{
    $user      ??= User::factory()->create();
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

it('returns the most recent orders', function () {
    createOrderForUser(overrides: ['created_at' => now()->subDays(3)]);
    createOrderForUser(overrides: ['created_at' => now()->subDays(2)]);
    $newest = createOrderForUser(overrides: ['created_at' => now()->subDay()]);

    $results = (new RecentOrdersQuery)->get();

    expect($results->first()['id'])->toBe($newest->id);
});

it('limits results to the given count', function () {
    foreach (range(1, 8) as $i) {
        createOrderForUser();
    }

    $results = (new RecentOrdersQuery(limit: 3))->get();

    expect($results)->toHaveCount(3);
});

it('defaults to 5 recent orders', function () {
    foreach (range(1, 7) as $i) {
        createOrderForUser();
    }

    $results = (new RecentOrdersQuery)->get();

    expect($results)->toHaveCount(5);
});

it('returns the expected shape for each order', function () {
    $user  = User::factory()->create(['name' => 'Jane Doe']);
    $order = createOrderForUser($user, ['total' => 200.00, 'status' => OrderStatusEnum::SHIPPED]);

    $result = (new RecentOrdersQuery(limit: 1))->get()->first();

    expect($result)->toMatchArray([
        'id'           => $order->id,
        'order_number' => $order->order_number,
        'user_name'    => 'Jane Doe',
        'total'        => '200.00',
        'status'       => OrderStatusEnum::SHIPPED->value,
    ])
    ->and($result)->toHaveKey('created_at');
});

it('returns empty collection when no orders exist', function () {
    $results = (new RecentOrdersQuery)->get();

    expect($results)->toBeEmpty();
});
