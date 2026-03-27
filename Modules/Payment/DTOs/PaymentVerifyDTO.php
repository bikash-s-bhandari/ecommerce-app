<?php

namespace Modules\Payment\DTOs;

/**
 * Input DTO for verifying a payment after the gateway redirects back.
 *
 * - gatewayRef    : what was returned in PaymentInitiateResultDTO::$gatewayRef
 *                   Stripe → paymentIntentId
 *                   eSewa  → transaction_uuid
 *                   Khalti → pidx
 *
 * - callbackData  : raw query/body params sent by the gateway on callback
 *                   eSewa  → ['data' => '<base64 encoded JSON>']
 *                   Khalti → ['pidx' => '...', 'status' => '...', ...]
 *                   Stripe → not used (webhook handles it) but can pass intent
 */
readonly class PaymentVerifyDTO
{
    public function __construct(
        public string $gatewayRef,
        public array $callbackData = [],
    ) {}
}
