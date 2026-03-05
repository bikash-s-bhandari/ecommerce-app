<?php

namespace Modules\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\DTOs\ProductFilterDTO;

class PriceRangeFilter implements ProductQueryInterface
{
    public function __construct(private ProductFilterDTO $filter) {}

    public function apply(Builder $query): Builder
    {
        if ($this->filter->minPrice !== null) {
            $query->where('price', '>=', $this->filter->minPrice);
        }
        if ($this->filter->maxPrice !== null) {
            $query->where('price', '<=', $this->filter->maxPrice);
        }
        return $query;
    }
}
