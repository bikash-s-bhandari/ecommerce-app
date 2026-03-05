<?php

namespace Modules\Order\Actions;

use App\Exceptions\BusinessException;
use Modules\Order\DTOs\UpdateOrderStatusDTO;
use Modules\Order\Events\OrderShipped;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;

class UpdateOrderStatusAction
{
    public function execute(Order $order, UpdateOrderStatusDTO $dto): Order
    {
        if (!$order->status->canTransitionTo($dto->status)) {
            throw new BusinessException(
                "Cannot transition from {$order->status->label()} to {$dto->status->label()}.",
                422
            );
        }
        $order->update(['status' => $dto->status]);
        // Fire relevant events
        if ($dto->status === OrderStatusEnum::SHIPPED) {
            OrderShipped::dispatch($order);
        }
        return $order->fresh();
    }
}
