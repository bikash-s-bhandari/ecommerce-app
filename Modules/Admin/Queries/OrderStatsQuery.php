<?php

namespace Modules\Admin\Queries;

use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;

class OrderStatsQuery
{
    public function get(): object
    {
        return Order::toBase()
            ->selectRaw('COUNT(*) as total_orders')
            ->selectRaw("SUM(CASE WHEN status != 'cancelled' THEN total ELSE 0 END) as total_revenue")
            ->selectRaw("SUM(CASE WHEN DATE(created_at) = CURDATE() AND status != 'cancelled' THEN total ELSE 0 END) as today_revenue")
            ->selectRaw("SUM(CASE WHEN created_at >= ? AND status != 'cancelled' THEN total ELSE 0 END) as week_revenue", [now()->startOfWeek()])
            ->selectRaw("SUM(CASE WHEN MONTH(created_at) = ? AND status != 'cancelled' THEN total ELSE 0 END) as month_revenue", [now()->month])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as pending_orders', [OrderStatusEnum::PENDING->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as processing_orders', [OrderStatusEnum::PROCESSING->value])
            ->selectRaw('COUNT(CASE WHEN status = ? THEN 1 END) as shipped_orders', [OrderStatusEnum::SHIPPED->value])
            ->first();
    }
}
