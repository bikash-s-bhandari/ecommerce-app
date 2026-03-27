<?php

namespace Modules\Payment\Contracts;

use Modules\Payment\DTOs\PaymentInitiateDTO;
use Modules\Payment\DTOs\PaymentInitiateResultDTO;
use Modules\Payment\DTOs\PaymentVerifyDTO;
use Modules\Payment\DTOs\PaymentVerifyResultDTO;
use Modules\Payment\Exceptions\PaymentFailedException;

/**
 * LSP (Liskov Substitution Principle) Contract:
 *
 * EVERY gateway (Stripe, eSewa, Khalti, PayPal, …) MUST:
 *   1. Accept the same input DTOs (PaymentInitiateDTO / PaymentVerifyDTO)
 *   2. Return the same output DTOs (PaymentInitiateResultDTO / PaymentVerifyResultDTO)
 *   3. Throw ONLY PaymentFailedException on error – never a bare \Exception
 *   4. Never return null where the DTO says string/float/array
 *
 * Callers (ProcessPaymentAction, VerifyPaymentAction) depend only on this
 * contract.  Swapping Stripe → eSewa → Khalti requires ZERO changes in those
 * callers – only the binding in PaymentServiceProvider changes.
 */
interface PaymentGatewayInterface
{
    /**
     * Initiate a payment session with the gateway.
     *
     * Stripe  → creates a PaymentIntent, returns clientSecret for frontend JS.
     * eSewa   → builds signed form fields, returns formData + redirectUrl.
     * Khalti  → calls Khalti API, returns redirectUrl (payment_url).
     *
     * @throws PaymentFailedException
     */
    public function initiate(PaymentInitiateDTO $dto): PaymentInitiateResultDTO;

    /**
     * Verify the payment status after the gateway redirects back.
     *
     * Stripe  → retrieves the PaymentIntent by ID and maps its status.
     * eSewa   → decodes base64 callback data, verifies HMAC signature.
     * Khalti  → calls Khalti lookup API with pidx.
     *
     * @throws PaymentFailedException
     */
    public function verify(PaymentVerifyDTO $dto): PaymentVerifyResultDTO;

    /**
     * Issue a full or partial refund.
     *
     * @throws PaymentFailedException
     */
    public function refund(string $transactionId, float $amount): bool;
}
