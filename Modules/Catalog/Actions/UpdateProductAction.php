<?php

namespace Modules\Catalog\Actions;

use Modules\Catalog\DTOs\ProductDTO;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\ProductRepositoryInterface;

class UpdateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}
    public function execute(Product $product, ProductDTO $dto): Product
    {
        return $this->productRepository->update($product, $dto);
    }
}
