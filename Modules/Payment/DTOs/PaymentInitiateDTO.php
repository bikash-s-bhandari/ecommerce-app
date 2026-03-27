<?php

namespace Modules\Payment\DTOs;

/**
 * Gateway-agnostic input DTO for initiating any payment.
 *
 * The `gateway` field carries the user's choice from the request
 * (e.g. "esewa", "khalti", "stripe") so ProcessPaymentAction can
 * ask the Factory for the correct implementation at runtime.
 *
 * - Stripe  : needs paymentMethodId (card token from Stripe.js)
 * - eSewa   : needs returnUrl (redirect back after payment)
 * - Khalti  : needs returnUrl (redirect back after payment)
 *
 * paymentMethodId is optional because redirect-based gateways
 * (eSewa, Khalti) don't need a pre-tokenized payment method.
 */
readonly class PaymentInitiateDTO
{
    public function __construct(
        public int $orderId,
        public float $amount,
        public string $currency,
        public string $returnUrl,
        public string $gateway,
        public ?string $paymentMethodId = null,
    ) {}
}
