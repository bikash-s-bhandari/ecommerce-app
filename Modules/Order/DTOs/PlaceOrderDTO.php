<?php

namespace Modules\Order\DTOs;

use Illuminate\Http\Request;
use Modules\Payment\Factories\PaymentGatewayFactory;

readonly class PlaceOrderDTO
{
    public function __construct(
        public int $userId,
        public array $shippingAddress,
        public string $gateway,           // 'stripe' | 'esewa' | 'khalti'
        public ?string $paymentToken,     // Stripe PaymentMethod ID; null for eSewa/Khalti
        public ?string $notes = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        $request->validate([
            'payment_gateway' => ['required', 'string', 'in:' . implode(',', PaymentGatewayFactory::SUPPORTED)],
        ]);

        return new self(
            userId:         $request->user()->id,
            shippingAddress: $request->input('shipping_address'),
            gateway:         $request->input('payment_gateway'),
            paymentToken:    $request->input('payment_token'),   // optional for redirect gateways
            notes:           $request->input('notes'),
        );
    }
}
