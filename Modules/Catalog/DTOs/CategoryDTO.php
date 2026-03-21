<?php

namespace Modules\Catalog\DTOs;

use Illuminate\Http\Request;

readonly class CategoryDTO
{
    public function __construct(
        public string $name,
        public ?string $description = null,
        public ?int $parentId = null,
        public bool $isActive = true,
        public int $sortOrder = 0,
    ) {}
    public static function fromRequest(Request $request): self
    {
        return new self(
            name: $request->input('name'),
            description: $request->input('description'),
            parentId: $request->input('parent_id'),
            isActive: (bool) $request->input('is_active', true),
            sortOrder: (int) $request->input('sort_order', 0),
        );
    }
}
