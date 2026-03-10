<?php

use Modules\Order\Enums\OrderStatusEnum;

it('allows valid order status transitions', function () {
    expect(OrderStatusEnum::PENDING->canTransitionTo(OrderStatusEnum::CONFIRMED))->toBeTrue();
    expect(OrderStatusEnum::CONFIRMED->canTransitionTo(OrderStatusEnum::PROCESSING))->toBeTrue();
    expect(OrderStatusEnum::PROCESSING->canTransitionTo(OrderStatusEnum::SHIPPED))->toBeTrue();
    expect(OrderStatusEnum::SHIPPED->canTransitionTo(OrderStatusEnum::DELIVERED))->toBeTrue();
});
it('prevents invalid order status transitions', function () {
    expect(OrderStatusEnum::PENDING->canTransitionTo(OrderStatusEnum::DELIVERED))->toBeFalse();
    expect(OrderStatusEnum::DELIVERED->canTransitionTo(OrderStatusEnum::PENDING))->toBeFalse();
    expect(OrderStatusEnum::SHIPPED->canTransitionTo(OrderStatusEnum::CONFIRMED))->toBeFalse();
});
