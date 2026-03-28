<?php

namespace Modules\Cart\DTOs;

use Illuminate\Http\Request;

readonly class AddToCartDTO
{
    public function __construct(
        public int $productId,
        public int $quantity,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            productId: $request->input('product_id'),
            quantity: $request->input('quantity'),
        );
    }
}
