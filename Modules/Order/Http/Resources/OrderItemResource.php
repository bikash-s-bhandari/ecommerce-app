<?php

namespace Modules\Order\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class OrderItemResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'product_title' => $this->product_title,
            'product_sku' => $this->product_sku,
            'quantity' => $this->quantity,
            'unit_price' => (float) $this->unit_price,
            'subtotal' => (float) $this->subtotal,
        ];
    }
}
