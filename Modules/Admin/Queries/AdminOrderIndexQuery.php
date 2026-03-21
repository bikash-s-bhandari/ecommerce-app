<?php

namespace Modules\Admin\Queries;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Order\Models\Order;

class AdminOrderIndexQuery
{
    public function __construct(
        private readonly ?string $status = null,
        private readonly ?string $search = null,
        private readonly int $perPage = 20,
    ) {}

    public function paginate(): LengthAwarePaginator
    {
        return Order::with(['user', 'items', 'payment'])
            ->when($this->status, fn ($q, $v) => $q->where('status', $v))
            ->when($this->search, fn ($q) => $q->where(function ($sub) {
                $sub->where('order_number', 'like', "%{$this->search}%")
                    ->orWhereHas('user', fn ($uq) => $uq->where('email', 'like', "%{$this->search}%"));
            }))
            ->latest()
            ->paginate($this->perPage);
    }
}
