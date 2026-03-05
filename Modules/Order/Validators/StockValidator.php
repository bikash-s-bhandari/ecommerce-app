<?php

namespace Modules\Order\Validators;

use App\Exceptions\BusinessException;
use Modules\Order\DTOs\PlaceOrderDTO;

class StockValidator extends OrderValidatorHandler
{
    public function handle(PlaceOrderDTO $dto, array $cartItems): void
    {
        foreach ($cartItems as $item) {
            if ($item->product->stock < $item->quantity) {
                throw new BusinessException(
                    "{$item->product->title}: only {$item->product->stock} units available.",
                    422
                );
            }
        }
        $this->passToNext($dto, $cartItems);
    }
}
