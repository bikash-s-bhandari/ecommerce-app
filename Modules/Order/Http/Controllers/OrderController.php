<?php

namespace Modules\Order\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Order\Actions\PlaceOrderAction;
use Modules\Order\DTOs\PlaceOrderDTO;
use Modules\Order\Http\Resources\OrderResource;
use Modules\Order\Models\Order;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orders = Order::with(['items', 'payment'])
            ->where('user_id', $request->user()->id)
            ->latest()
            ->paginate(10);

        return $this->success(OrderResource::collection($orders)->response()->getData(true));
    }
    public function show(Request $request, int $id): JsonResponse
    {
        $order = Order::with(['items', 'payment'])
            ->where('user_id', $request->user()->id)
            ->findOrFail($id);

        return $this->success(OrderResource::make($order));
    }

    public function store(Request $request, PlaceOrderAction $action): JsonResponse
    {
        $request->validate([
            'payment_token' => ['required', 'string'],
            'shipping_address' => ['required', 'array'],
            'shipping_address.full_name' => ['required', 'string', 'max:100'],
            'shipping_address.street_1' => ['required', 'string', 'max:200'],
            'shipping_address.street_2' => ['nullable', 'string', 'max:200'],
            'shipping_address.city' => ['required', 'string', 'max:100'],
            'shipping_address.state' => ['required', 'string', 'max:100'],
            'shipping_address.postal_code' => ['required', 'string', 'max:20'],
            'shipping_address.country' => ['required', 'string', 'max:100'],
            'notes' => ['nullable', 'string', 'max:500'],
        ]);

        $order = $action->execute(PlaceOrderDTO::fromRequest($request));

        return $this->created(
            OrderResource::make($order->load(['items', 'payment'])),
            'Order placed successfully'
        );
    }
}
