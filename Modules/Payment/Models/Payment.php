<?php

namespace Modules\Payment\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Order\Models\Order;
use Modules\Payment\Enums\PaymentStatusEnum;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'status',
        'gateway',
        'transaction_id',
        'payment_method',
        'amount',
        'currency',
        'gateway_response',
        'idempotency_key',
        'paid_at',
    ];
    protected function casts(): array
    {
        return [
            'status' => PaymentStatusEnum::class,
            'amount' => 'decimal:2',
            'gateway_response' => 'array',
            'paid_at' => 'datetime',
        ];
    }
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}
