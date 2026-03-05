<?php

namespace Modules\Order\Actions;

use Illuminate\Support\Facades\DB;
use Modules\Cart\Repositories\CartRepository;
use Modules\Catalog\Repositories\ProductRepositoryInterface;
use Modules\Order\DTOs\PlaceOrderDTO;
use Modules\Order\Events\OrderPlaced;
use Modules\Order\Models\Order;
use Modules\Order\Models\OrderItem;
use Modules\Order\Enums\OrderStatusEnum;
use Modules\Order\Validators\{AddressValidator, CartNotEmptyValidator, StockValidator};
// use Modules\Payment\Actions\ProcessPaymentAction;
// use Modules\Payment\DTOs\ProcessPaymentDTO;

class PlaceOrderAction
{
    public function __construct(
        private CartRepository $cartRepository,
        private ProductRepositoryInterface $productRepository,
        // private ProcessPaymentAction $processPaymentAction,
    ) {}

    public function execute(PlaceOrderDTO $dto): Order
    {
        return DB::transaction(function () use ($dto) {
            // 1. Resolve cart
            $cart = DB::table('carts')->where('user_id', $dto->userId)->first();

            $cartItems = DB::table('cart_items')
                ->where('cart_id', $cart->id)
                ->join('products', 'products.id', '=', 'cart_items.product_id')
                ->select('cart_items.*', 'products.title as product_title', 'products.sku as product_sku', 'products.stock')
                ->get()->toArray();

            // 2. Validate via Chain of Responsibility
            $validator = new CartNotEmptyValidator();
            $stockValidator = new StockValidator();
            $addressValidator = new AddressValidator();
            $validator->setNext($stockValidator)->setNext($addressValidator);
            $validator->handle($dto, $cartItems);

            // 3. Calculate totals
            $subtotal = collect($cartItems)->sum(fn($i) => $i->unit_price * $i->quantity);
            $tax = round($subtotal * 0.1, 2); // 10% tax
            $total = $subtotal + $tax;

            // 4. Create order
            $order = Order::create([
                'user_id' => $dto->userId,
                'shipping_address' => $dto->shippingAddress,
                'status' => OrderStatusEnum::PENDING,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'total' => $total,
                'notes' => $dto->notes,
            ]);

            // 5. Create order items + decrement stock
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'product_title' => $item->product_title,
                    'product_sku' => $item->product_sku,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => $item->unit_price * $item->quantity,
                ]);
                $this->productRepository->decrementStock($item->product_id, $item->quantity);
            }

            // 6. Process payment
            // $this->processPaymentAction->execute(new ProcessPaymentDTO(
            //     orderId: $order->id,
            //     amount: $total,
            //     currency: 'usd',
            //     paymentMethodId: $dto->paymentToken,
            // ));
            // 7. Clear cart
            $this->cartRepository->clear($cart->id);
            // 8. Dispatch event (queued notification)
            OrderPlaced::dispatch($order->load(['items', 'user', 'payment']));

            return $order;
        });
    }
}
