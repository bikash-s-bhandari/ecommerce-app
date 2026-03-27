<?php

namespace Modules\Payment\Factories;

use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\Gateways\EsewaGateway;
use Modules\Payment\Gateways\KhaltiGateway;
use Modules\Payment\Gateways\StripeGateway;

/**
 * Resolves the correct PaymentGateway at runtime based on the user's choice.
 *
 * WHY a Factory instead of Service Provider binding?
 * ─────────────────────────────────────────────────
 * Service Provider binding is a single global binding: one interface → one class.
 * That works when the whole application uses ONE gateway.
 *
 * In ecommerce, the USER chooses at checkout:
 *   { "payment_gateway": "esewa" }   ← eSewa
 *   { "payment_gateway": "khalti" }  ← Khalti
 *   { "payment_gateway": "stripe" }  ← Stripe
 *
 * So we need to resolve the correct gateway PER REQUEST.
 * The Factory does exactly that: call make('esewa') → EsewaGateway instance.
 *
 * SOLID connections:
 *   - Single Responsibility : factory only builds gateways, nothing else
 *   - Open/Closed           : add a new gateway by adding one match arm, no existing code changes
 *   - Dependency Inversion  : callers depend on the interface, not the concrete class
 */
class PaymentGatewayFactory
{
    /**
     * Supported gateways (kept in sync with config/config.php → gateways keys).
     */
    public const SUPPORTED = ['stripe', 'esewa', 'khalti'];

    public function make(string $driver): PaymentGatewayInterface
    {
        return match ($driver) {
            'stripe' => new StripeGateway(),
            'esewa'  => new EsewaGateway(),
            'khalti' => new KhaltiGateway(),
            default  => throw new \InvalidArgumentException(
                "Unsupported payment gateway: [{$driver}]. Supported: " . implode(', ', self::SUPPORTED)
            ),
        };
    }
}
