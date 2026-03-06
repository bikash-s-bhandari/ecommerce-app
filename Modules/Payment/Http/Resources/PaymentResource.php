<?php

namespace Modules\Payment\Http\Resources;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class PaymentResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'status' => $this->status->value,
            'status_label' => $this->status->label(),
            'gateway' => $this->gateway,
            'transaction_id' => $this->transaction_id,
            'amount' => (float) $this->amount,
            'currency' => strtoupper($this->currency),
            'paid_at' => $this->paid_at?->toIso8601String(),
        ];
    }
}
