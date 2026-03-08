<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Auth\Models\User;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Catalog\Models\Product;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $orderStats = Order::toBase()
            ->selectRaw("COUNT(*) as total_orders")
            ->selectRaw("SUM(CASE WHEN status != 'cancelled' THEN total ELSE 0 END) as total_revenue")
            ->selectRaw("SUM(CASE WHEN DATE(created_at) = CURDATE() AND status != 'cancelled' THEN total ELSE 0 END) as today_revenue")
            ->selectRaw("SUM(CASE WHEN created_at >= ? AND status != 'cancelled' THEN total ELSE 0 END) as week_revenue", [now()->startOfWeek()])
            ->selectRaw("SUM(CASE WHEN MONTH(created_at) = ? AND status != 'cancelled' THEN total ELSE 0 END) as month_revenue", [now()->month])
            ->selectRaw("COUNT(CASE WHEN status = ? THEN 1 END) as pending_orders", [OrderStatusEnum::PENDING->value])
            ->selectRaw("COUNT(CASE WHEN status = ? THEN 1 END) as processing_orders", [OrderStatusEnum::PROCESSING->value])
            ->selectRaw("COUNT(CASE WHEN status = ? THEN 1 END) as shipped_orders", [OrderStatusEnum::SHIPPED->value])
            ->first();

        $userStats = User::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("COUNT(CASE WHEN created_at >= ? THEN 1 END) as new_week", [now()->startOfWeek()])
            ->first();

        $productStats = Product::toBase()
            ->selectRaw("COUNT(*) as total")
            ->selectRaw("COUNT(CASE WHEN stock <= low_stock_threshold AND stock > 0 THEN 1 END) as low_stock")
            ->selectRaw("COUNT(CASE WHEN stock = 0 THEN 1 END) as out_of_stock")
            ->first();

        $recentOrders = Order::with(['user', 'payment'])
            ->latest()
            ->limit(5)
            ->get()
            ->map(fn($o) => [
                'id'           => $o->id,
                'order_number' => $o->order_number,
                'user_name'    => $o->user->name,
                'total'        => $o->total,
                'status'       => $o->status->value,
                'created_at'   => $o->created_at->diffForHumans(),
            ]);

        return $this->success([
            'revenue' => [
                'today' => $orderStats->today_revenue ?? 0,
                'week'  => $orderStats->week_revenue ?? 0,
                'month' => $orderStats->month_revenue ?? 0,
                'total' => $orderStats->total_revenue ?? 0,
            ],
            'orders' => [
                'pending'    => $orderStats->pending_orders,
                'processing' => $orderStats->processing_orders,
                'shipped'    => $orderStats->shipped_orders,
                'total'      => $orderStats->total_orders,
            ],
            'users' => [
                'total'    => $userStats->total,
                'new_week' => $userStats->new_week,
            ],
            'products' => [
                'total'        => $productStats->total,
                'low_stock'    => $productStats->low_stock,
                'out_of_stock' => $productStats->out_of_stock,
            ],
            'recent_orders' => $recentOrders,
        ]);
    }
}
