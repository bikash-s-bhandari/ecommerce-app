<?php

namespace Modules\Order\Validators;

use App\Exceptions\BusinessException;
use Modules\Order\DTOs\PlaceOrderDTO;

class AddressValidator extends OrderValidatorHandler
{
    private array $required = [
        'full_name',
        'street_1',
        'city',
        'state',
        'postal_code',
        'country'
    ];
    public function handle(PlaceOrderDTO $dto, array $cartItems): void
    {
        foreach ($this->required as $field) {
            if (empty($dto->shippingAddress[$field])) {
                throw new BusinessException("Shipping address missing: {$field}.", 422);
            }
        }
        $this->passToNext($dto, $cartItems);
    }
}
