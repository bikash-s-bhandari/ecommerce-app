<?php

namespace Modules\Payment\Contracts;

use Modules\Payment\DTOs\ProcessPaymentDTO;

interface PaymentGatewayInterface
{
    public function createIntent(ProcessPaymentDTO $dto): array;
    public function confirm(string $paymentIntentId): array;
    public function refund(string $transactionId, float $amount): bool;
}
