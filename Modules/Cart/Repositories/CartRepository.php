<?php

namespace Modules\Cart\Repositories;

use Illuminate\Http\Request;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Illuminate\Support\Str;

class CartRepository
{
    public function __construct(protected Cart $model) {}

    public function resolveCart(Request $request): Cart
    {
        $user = $request->user('sanctum');

        if ($user) {
            return $this->model->firstOrCreate(['user_id' => $user->id]);
        }
        $sessionId = $request->cookie('cart_session', Str::uuid());

        $cart = $this->model->firstOrCreate(['session_id' => $sessionId]);

        cookie()->queue('cart_session', $sessionId, 60 * 24 * 30);

        return $cart;
    }

    public function addItem(Cart $cart, int $productId, int $qty, float $price): CartItem
    {
        $item = $cart->items()->where('product_id', $productId)->first();
        if ($item) {
            $item->increment('quantity', $qty);

            return $item;
        }
        return CartItem::create([
            'cart_id' => $cart->id,
            'product_id' => $productId,
            'quantity' => $qty,
            'unit_price' => $price,
        ]);
    }

    public function updateItem(CartItem $item, int $qty): CartItem
    {
        $item->update(['quantity' => $qty]);

        return $item;
    }
    public function removeItem(CartItem $item): void
    {
        $item->delete();
    }

    public function clear(int $cartId): void
    {
        CartItem::where('cart_id', $cartId)->delete();
    }
    public function mergeGuestCart(string $sessionId, int $userId): void
    {
        $guestCart = Cart::where('session_id', $sessionId)->with('items')->first();

        if (!$guestCart) return;

        $userCart = Cart::firstOrCreate(['user_id' => $userId]);

        foreach ($guestCart->items as $item) {
            $existing = $userCart->items()->where('product_id', $item->product_id)->first();
            if ($existing) {
                $existing->increment('quantity', $item->quantity);
            } else {
                $item->update(['cart_id' => $userCart->id]);
            }
        }
        $guestCart->delete();
    }
}
