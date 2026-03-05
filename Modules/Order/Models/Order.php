<?php

namespace Modules\Order\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Order\Enums\OrderStatusEnum;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, HasMany};
use Modules\Auth\Models\User;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'user_id',
        'shipping_address',
        'status',
        'subtotal',
        'tax',
        'shipping_fee',
        'total',
        'notes',
    ];
    protected function casts(): array
    {
        return [
            'shipping_address' => 'array',
            'status' => OrderStatusEnum::class,
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->order_number ??= 'ORD-' .
            strtoupper(Str::random(10)));
    }
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
    // public function payment(): HasOne
    // {
    //     return $this->hasOne(Payment::class);
    // }
}
