<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;
use Modules\Auth\Models\User;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Models\Order;
use Modules\Catalog\Models\Product;

class AdminDashboardController extends Controller
{
    public function index(): JsonResponse
    {
        $stats = Cache::remember('admin:dashboard', 300, function () {
            return [
                'revenue' => [
                    'today' => Order::whereDate('created_at', today())->where(
                        'status',
                        '!=',
                        'cancelled'
                    )->sum('total'),
                    'week' => Order::whereBetween('created_at', [
                        now()->startOfWeek(),
                        now()
                    ])->sum('total'),
                    'month' => Order::whereMonth('created_at', now()->month)->sum('total'),
                    'total' => Order::where('status', '!=', 'cancelled')->sum('total'),
                ],
                'orders' => [
                    'pending' => Order::where('status', OrderStatusEnum::PENDING)->count(),
                    'processing' => Order::where('status', OrderStatusEnum::PROCESSING)->count(),
                    'shipped' => Order::where('status', OrderStatusEnum::SHIPPED)->count(),
                    'total' => Order::count(),
                ],
                'users' => [
                    'total' => User::count(),
                    'new_week' => User::where('created_at', '>=', now()->startOfWeek())->count(),
                ],
                'products' => [
                    'total' => Product::count(),
                    'low_stock' => Product::whereColumn(
                        'stock',
                        '<=',
                        'low_stock_threshold'
                    )->where('stock', '>', 0)->count(),
                    'out_of_stock' => Product::where('stock', 0)->count(),
                ],
                'recent_orders' => Order::with(['user', 'payment'])->latest()->limit(5)->get()->map(fn($o) => [
                    'id' => $o->id,
                    'order_number' => $o->order_number,
                    'user_name' => $o->user->name,
                    'total' => $o->total,
                    'status' => $o->status->value,
                    'created_at' => $o->created_at->diffForHumans(),
                ]),
            ];
        });

        return $this->success($stats);
    }
}
