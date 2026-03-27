<?php

namespace Modules\Payment\Exceptions;

/**
 * Thrown when any payment gateway fails to process or verify a payment.
 * All gateways MUST throw this (not a generic \Exception) so callers
 * can catch one consistent exception type – this is the LSP contract.
 */
class PaymentFailedException extends \RuntimeException
{
    public function __construct(
        string $message = 'Payment failed',
        int $code = 0,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
    }
}
