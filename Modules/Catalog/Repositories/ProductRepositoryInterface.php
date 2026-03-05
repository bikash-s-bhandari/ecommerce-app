<?php

namespace Modules\Catalog\Repositories;

use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Modules\Catalog\DTOs\ProductDTO;
use Modules\Catalog\DTOs\ProductFilterDTO;
use Modules\Catalog\Models\Product;

interface ProductRepositoryInterface
{
    public function findById(int $id): Product;
    public function findBySlug(string $slug): Product;
    public function search(ProductFilterDTO $filter): LengthAwarePaginator;
    public function create(ProductDTO $dto): Product;
    public function update(Product $product, ProductDTO $dto): Product;
    public function delete(Product $product): void;
    public function decrementStock(int $productId, int $qty): void;
}
