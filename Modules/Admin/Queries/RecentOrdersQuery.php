<?php

namespace Modules\Admin\Queries;

use Illuminate\Support\Collection;
use Modules\Order\Models\Order;

class RecentOrdersQuery
{
    public function __construct(private readonly int $limit = 5) {}

    public function get(): Collection
    {
        return Order::with(['user', 'payment'])
            ->latest()
            ->limit($this->limit)
            ->get()
            ->map(fn ($order) => [
                'id'           => $order->id,
                'order_number' => $order->order_number,
                'user_name'    => $order->user->name,
                'total'        => $order->total,
                'status'       => $order->status->value,
                'created_at'   => $order->created_at->diffForHumans(),
            ]);
    }
}
