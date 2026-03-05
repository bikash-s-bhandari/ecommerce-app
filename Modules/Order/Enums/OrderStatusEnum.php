<?php

namespace Modules\Order\Enums;

enum OrderStatusEnum: string
{
    case PENDING = 'pending';
    case CONFIRMED = 'confirmed';
    case PROCESSING = 'processing';
    case SHIPPED = 'shipped';
    case DELIVERED = 'delivered';
    case CANCELLED = 'cancelled';
    case REFUNDED = 'refunded';

    public function label(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::CONFIRMED => 'Confirmed',
            self::PROCESSING => 'Processing',
            self::SHIPPED => 'Shipped',
            self::DELIVERED => 'Delivered',
            self::CANCELLED => 'Cancelled',
            self::REFUNDED => 'Refunded',
        };
    }

    public function canTransitionTo(self $next): bool
    {
        $allowed = [
            self::PENDING->value => [self::CONFIRMED->value, self::CANCELLED->value],
            self::CONFIRMED->value => [self::PROCESSING->value, self::CANCELLED->value],
            self::PROCESSING->value => [self::SHIPPED->value],
            self::SHIPPED->value => [self::DELIVERED->value],
            self::DELIVERED->value => [self::REFUNDED->value],
        ];

        return in_array($next->value, $allowed[$this->value] ?? []);
    }
}
