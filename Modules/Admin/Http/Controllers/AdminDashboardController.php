<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Admin\Queries\OrderStatsQuery;
use Modules\Admin\Queries\ProductStatsQuery;
use Modules\Admin\Queries\RecentOrdersQuery;
use Modules\Admin\Queries\UserStatsQuery;

class AdminDashboardController extends Controller
{
    //query object banayera use gareko
    public function index(
        OrderStatsQuery $orderStats,
        UserStatsQuery $userStats,
        ProductStatsQuery $productStats,
        RecentOrdersQuery $recentOrders,
    ): JsonResponse {
        $orders   = $orderStats->get();
        $users    = $userStats->get();
        $products = $productStats->get();

        return $this->success([
            'revenue' => [
                'today' => $orders->today_revenue ?? 0,
                'week'  => $orders->week_revenue ?? 0,
                'month' => $orders->month_revenue ?? 0,
                'total' => $orders->total_revenue ?? 0,
            ],
            'orders' => [
                'pending'    => $orders->pending_orders,
                'processing' => $orders->processing_orders,
                'shipped'    => $orders->shipped_orders,
                'total'      => $orders->total_orders,
            ],
            'users' => [
                'total'    => $users->total,
                'new_week' => $users->new_week,
            ],
            'products' => [
                'total'        => $products->total,
                'low_stock'    => $products->low_stock,
                'out_of_stock' => $products->out_of_stock,
            ],
            'recent_orders' => $recentOrders->get(),
        ]);
    }
}
