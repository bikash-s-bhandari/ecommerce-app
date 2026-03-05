<?php

namespace Modules\Order\DTOs;

use Illuminate\Http\Request;

readonly class PlaceOrderDTO
{
    public function __construct(
        public int $userId,
        public array $shippingAddress,
        public string $paymentToken, // Stripe PaymentMethod ID
        public ?string $notes = null,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            userId: $request->user()->id,
            shippingAddress: $request->validated('shipping_address'),
            paymentToken: $request->validated('payment_token'),
            notes: $request->validated('notes'),
        );
    }
}
