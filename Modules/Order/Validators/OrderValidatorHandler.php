<?php

namespace Modules\Order\Validators;

use Modules\Order\DTOs\PlaceOrderDTO;

//for chain of responsibility pattern
abstract class OrderValidatorHandler
{
    protected ?OrderValidatorHandler $next = null;

    public function setNext(OrderValidatorHandler $handler): OrderValidatorHandler
    {
        $this->next = $handler;

        return $handler;
    }

    abstract public function handle(PlaceOrderDTO $dto, array $cartItems): void;

    protected function passToNext(PlaceOrderDTO $dto, array $cartItems): void
    {
        $this->next?->handle($dto, $cartItems);
    }
}
