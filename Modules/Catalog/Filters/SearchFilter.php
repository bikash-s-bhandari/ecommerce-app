<?php

namespace Modules\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;
use Modules\Catalog\DTOs\ProductFilterDTO;

class SearchFilter implements ProductQueryInterface
{
    public function __construct(private ProductFilterDTO $filter) {}

    public function apply(Builder $query): Builder
    {
        if ($this->filter->search) {
            $query->whereFullText(['title', 'description'], $this->filter->search);//['title', 'description'] = columns jaha keyword search garna cha.
        }
        //SQL: SELECT * FROM products WHERE MATCH(title, description) AGAINST('user keyword' IN NATURAL LANGUAGE MODE);
        return $query;
    }
}
