<?php

namespace Modules\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\DTOs\ProductFilterDTO;

class TagFilter implements ProductQueryInterface
{
    public function __construct(private ProductFilterDTO $filter) {}

    public function apply(Builder $query): Builder
    {
        if ($this->filter->tagIds) {
            $query->whereHas('tags', fn($q) => $q->whereIn('tags.id', $this->filter->tagIds));
        }
        return $query;
    }
}
