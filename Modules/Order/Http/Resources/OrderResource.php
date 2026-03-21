<?php

namespace Modules\Order\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;
use Modules\Payment\Http\Resources\PaymentResource;

class OrderResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'order_number' => $this->order_number,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'shipping_address' => $this->shipping_address,
            'subtotal' => (float) $this->subtotal,
            'tax' => (float) $this->tax,
            'shipping_fee' => (float) $this->shipping_fee,
            'total' => (float) $this->total,
            'notes' => $this->notes,
            'items' => OrderItemResource::collection($this->whenLoaded('items')),
            'payment' =>PaymentResource::make($this->whenLoaded('payment')),
            'created_at' => $this->created_at->toIso8601String(),
            'updated_at' => $this->updated_at->toIso8601String(),
        ];
    }
}
