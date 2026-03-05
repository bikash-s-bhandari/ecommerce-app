<?php

namespace Modules\Catalog\Filters;

use Illuminate\Database\Eloquent\Builder;

//Filtes folder vitra vako sabai class laravel decorator pattern use huncha.
interface ProductQueryInterface
{
    public function apply(Builder $query): Builder;
}
