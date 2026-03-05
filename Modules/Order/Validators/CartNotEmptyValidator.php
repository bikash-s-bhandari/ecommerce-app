<?php

namespace Modules\Order\Validators;

use App\Exceptions\BusinessException;
use Modules\Order\DTOs\PlaceOrderDTO;

class CartNotEmptyValidator extends OrderValidatorHandler
{
    public function handle(PlaceOrderDTO $dto, array $cartItems): void
    {
        if (empty($cartItems)) {
            throw new BusinessException('Cannot place order with empty cart.', 422);
        }
        $this->passToNext($dto, $cartItems);
    }
}
