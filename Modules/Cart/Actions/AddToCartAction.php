<?php

namespace Modules\Cart\Actions;

use App\Exceptions\BusinessException;
use Illuminate\Http\Request;
use Modules\Cart\DTOs\AddToCartDTO;
use Modules\Cart\Models\Cart;
use Modules\Cart\Repositories\CartRepository;
use Modules\Catalog\Repositories\ProductRepositoryInterface;

class AddToCartAction
{
    public function __construct(
        private CartRepository $cartRepository,
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(Request $request, AddToCartDTO $dto): Cart
    {
        $product = $this->productRepository->findById($dto->productId);

        if (!$product->isInStock()) {
            throw new BusinessException('Product is out of stock.', 422);
        }

        if ($product->stock < $dto->quantity) {
            throw new BusinessException("Only {$product->stock} units available.", 422);
        }

        $cart = $this->cartRepository->resolveCart($request);

        $this->cartRepository->addItem($cart, $dto->productId, $dto->quantity, $product->effectivePrice());

        return $cart->load('items.product.images');
    }
}
