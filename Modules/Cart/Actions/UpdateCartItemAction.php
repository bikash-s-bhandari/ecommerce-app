<?php

namespace Modules\Cart\Actions;

use App\Exceptions\BusinessException;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Cart\Repositories\CartRepository;
use Modules\Catalog\Repositories\ProductRepositoryInterface;

class UpdateCartItemAction
{
    public function __construct(
        private CartRepository $cartRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(CartItem $item, int $quantity): Cart
    {
        $product = $this->productRepository->findById($item->product_id);

        if ($product->stock < $quantity) {
            throw new BusinessException("Only {$product->stock} units available.", 422);
        }
        $this->cartRepository->updateItem($item, $quantity);

        return $item->cart->load('items.product.images');
    }
}
