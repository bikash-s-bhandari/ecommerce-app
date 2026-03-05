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
            name: $request->validated('name'),
            description: $request->validated('description'),
            parentId: $request->validated('parent_id'),
            isActive: (bool) $request->validated('is_active', true),
            sortOrder: (int) $request->validated('sort_order', 0),
        );
    }
}
