<?php

namespace Modules\Catalog\Actions;

use Modules\Catalog\DTOs\ProductDTO;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Repositories\ProductRepositoryInterface;

class CreateProductAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(ProductDTO $dto): Product
    {
        return $this->productRepository->create($dto);
    }
}
