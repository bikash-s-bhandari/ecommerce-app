<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\Actions\UpdateOrderStatusAction;
use Modules\Order\DTOs\UpdateOrderStatusDTO;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;
use Illuminate\Validation\Rule;

class AdminOrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['user', 'items', 'payment'])
            ->when($request->query('status'), fn($q, $v) => $q->where('status', $v))
            ->when($request->query('search'), fn($q, $v) => $q->where(
                'order_number',
                'like',
                "%{$v}%"
            )
                ->orWhereHas('user', fn($uq) => $uq->where('email', 'like', "%{$v}%")))
            ->latest()
            ->paginate((int) $request->query('per_page', 20));

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }

    public function show(int $id): JsonResponse
    {
        $order = Order::with(['user', 'items', 'payment'])->findOrFail($id);
        return $this->success(OrderResource::make($order));
    }

    public function updateStatus(Request $request, int $id, UpdateOrderStatusAction $action): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(OrderStatusEnum::class)],
        ]);

        $order = Order::findOrFail($id);

        $order = $action->execute($order, new UpdateOrderStatusDTO(
            status: OrderStatusEnum::from($request->validated('status')),
        ));

        return $this->success(OrderResource::make($order), 'Status updated');
    }
}
