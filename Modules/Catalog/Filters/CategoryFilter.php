<?php

namespace Modules\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\DTOs\ProductFilterDTO;

class CategoryFilter implements ProductQueryInterface
{
    public function __construct(private ProductFilterDTO $filter) {}
    
    public function apply(Builder $query): Builder
    {
        if ($this->filter->categoryId) {
            $query->where('category_id', $this->filter->categoryId);
        }
        return $query;
    }
}
