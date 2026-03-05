<?php

namespace Modules\Cart\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Cart\Actions\AddToCartAction;
use Modules\Cart\Actions\UpdateCartItemAction;
use Modules\Cart\DTOs\AddToCartDTO;
use Modules\Cart\Http\Resources\CartResource;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Repositories\CartRepository;

class CartController extends Controller
{
    public function index(Request $request, CartRepository $cartRepo): JsonResponse
    {
        $cart = $cartRepo->resolveCart($request)->load('items.product.images');

        return $this->success(CartResource::make($cart));
    }
    public function addItem(Request $request, AddToCartAction $action): JsonResponse
    {
        $request->validate([
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'quantity' => ['required', 'integer', 'min:1', 'max:100'],
        ]);
        $cart = $action->execute($request, AddToCartDTO::fromRequest($request));

        return $this->success(CartResource::make($cart), 'Item added to cart');
    }

    public function updateItem(Request $request, CartItem $item, UpdateCartItemAction
    $action): JsonResponse
    {
        $request->validate(['quantity' => ['required', 'integer', 'min:1', 'max:100']]);
        $this->ensureCartItemBelongsToUser($request, $item);
        $cart = $action->execute($item, $request->validated('quantity'));

        return $this->success(CartResource::make($cart), 'Cart updated');
    }
    public function removeItem(Request $request, CartItem $item, CartRepository $cartRepo): JsonResponse
    {
        $this->ensureCartItemBelongsToUser($request, $item);
        $cartRepo->removeItem($item);

        return $this->success(null, 'Item removed from cart');
    }
    public function clear(Request $request, CartRepository $cartRepo): JsonResponse
    {
        $cart = $cartRepo->resolveCart($request);
        $cartRepo->clear($cart->id);

        return $this->success(null, 'Cart cleared');
    }

    private function ensureCartItemBelongsToUser(Request $request, CartItem $item): void
    {
        $cart = $this->resolveCartForRequest($request);
        if ($item->cart_id !== $cart->id) {
            abort(403, 'Unauthorized cart access');
        }
    }
    private function resolveCartForRequest(Request $request)
    {
        return app(CartRepository::class)->resolveCart($request);
    }
}
