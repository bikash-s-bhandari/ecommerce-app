<?php

namespace Modules\Payment\DTOs;

/**
 * Consistent result returned by ALL gateways after initiation.
 *
 * type = 'intent'   → Stripe:  use clientSecret with Stripe.js on the frontend
 * type = 'redirect' → eSewa/Khalti: redirect user to redirectUrl
 * type = 'form'     → eSewa:   render an auto-submit HTML form using formData
 *
 * Every gateway sets gatewayRef and raw.
 * Unused fields are null so callers must check type before using.
 *
 * LSP guarantee: ALL implementations return this exact DTO with these semantics.
 * Callers can safely switch(type) without knowing the concrete gateway.
 */
readonly class PaymentInitiateResultDTO
{
    public function __construct(
        public string $type,
        public string $gatewayRef,
        public ?string $redirectUrl,
        public ?string $clientSecret,
        public array $formData,
        public array $raw,
    ) {}
}
