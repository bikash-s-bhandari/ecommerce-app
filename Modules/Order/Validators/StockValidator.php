<?php

namespace Modules\Order\Validators;

use App\Exceptions\BusinessException;
use Modules\Order\DTOs\PlaceOrderDTO;

class StockValidator extends OrderValidatorHandler
{
    public function handle(PlaceOrderDTO $dto, array $cartItems): void
    {
        foreach ($cartItems as $item) {
            // $cartItems come from a joined query with product fields flattened
            // (see PlaceOrderAction). We use the selected stock/title columns here.
            if ($item->stock < $item->quantity) {
                throw new BusinessException(
                    "{$item->product_title}: only {$item->stock} units available.",
                    422
                );
            }
        }
        $this->passToNext($dto, $cartItems);
    }
}
