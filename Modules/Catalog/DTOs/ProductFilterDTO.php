<?php

namespace Modules\Catalog\DTOs;

use Illuminate\Http\Request;

readonly class ProductFilterDTO
{
    public function __construct(
        public ?string $search = null,
        public ?int $categoryId = null,
        public ?float $minPrice = null,
        public ?float $maxPrice = null,
        public ?array $tagIds = null,
        public ?bool $featured = null,
        public string $sortBy = 'created_at',
        public string $sortDir = 'desc',
        public int $perPage = 15,
    ) {}
    public static function fromRequest(Request $request): self
    {
        return new self(
            search: $request->query('search'),
            categoryId: $request->query('category_id'),
            minPrice: $request->query('min_price'),
            maxPrice: $request->query('max_price'),
            tagIds: $request->query('tag_ids'),
            featured: $request->boolean('featured') ?: null,
            sortBy: $request->query('sort_by', 'created_at'),
            sortDir: $request->query('sort_dir', 'desc'),
            perPage: (int) $request->query('per_page', 15),
        );
    }
}
