<?php

namespace Modules\Order\DTOs;

use Modules\Order\Enums\OrderStatusEnum;

readonly class UpdateOrderStatusDTO
{
    public function __construct(
        public OrderStatusEnum $status,
    ) {}
}
