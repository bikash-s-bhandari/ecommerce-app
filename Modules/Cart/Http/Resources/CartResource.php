<?php

namespace Modules\Cart\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Modules\Catalog\Http\Resources\ProductResource;

class CartResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'item_count' => $this->item_count,//Cart model ko accessor method
            'subtotal' => (float) $this->subtotal,
            'items' => $this->items->map(fn($item) => [
                'id' => $item->id,
                'quantity' => $item->quantity,
                'unit_price' => (float) $item->unit_price,
                'line_total' => (float) $item->line_total,
                'product' => ProductResource::make($item->product),
            ]),
        ];
    }
}
