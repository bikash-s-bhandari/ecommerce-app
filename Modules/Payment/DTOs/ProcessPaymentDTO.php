<?php

namespace Modules\Payment\DTOs;

readonly class ProcessPaymentDTO
{
    public function __construct(
        public int $orderId,
        public float $amount,
        public string $currency,
        public string $paymentMethodId,
    ) {}
}
