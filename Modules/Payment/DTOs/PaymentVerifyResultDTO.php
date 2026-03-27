<?php

namespace Modules\Payment\DTOs;

use Modules\Payment\Enums\PaymentStatusEnum;

/**
 * Consistent result returned by ALL gateways after verification.
 *
 * LSP guarantee: ALL implementations return this exact DTO.
 * VerifyPaymentAction can update the Payment model without knowing
 * which gateway was used.
 */
readonly class PaymentVerifyResultDTO
{
    public function __construct(
        public PaymentStatusEnum $status,
        public string $gatewayRef,
        public float $amount,
        public string $currency,
        public array $raw,
    ) {}
}
