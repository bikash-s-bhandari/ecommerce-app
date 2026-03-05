<?php

namespace Modules\Catalog\DTOs;

use Modules\Catalog\Enums\ProductStatusEnum;
use Modules\Catalog\Http\Requests\StoreProductRequest;
use Modules\Catalog\Http\Requests\UpdateProductRequest;

readonly class ProductDTO
{
    public function __construct(
        public string $title,
        public string $description,
        public float $price,
        public int $stock,
        public int $categoryId,
        public ProductStatusEnum $status,
        public ?float $salePrice = null,
        public ?string $sku = null,
        public bool $featured = false,
        public array $tagIds = [],
    ) {}
    public static function fromRequest(StoreProductRequest|UpdateProductRequest $request): self
    {
        return new self(
            title: $request->validated('title'),
            description: $request->validated('description'),
            price: $request->validated('price'),
            stock: $request->validated('stock'),
            categoryId: $request->validated('category_id'),
            status: ProductStatusEnum::from($request->validated('status', 'draft')),//// Usage $status = ProductStatusEnum::from('draft');// returns ProductStatusEnum::DRAFT
            salePrice: $request->validated('sale_price'),
            sku: $request->validated('sku'),
            featured: (bool) $request->validated('featured', false),
            tagIds: $request->validated('tag_ids', []),
        );
    }
}
