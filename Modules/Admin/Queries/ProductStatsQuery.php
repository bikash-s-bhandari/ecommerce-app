<?php

namespace Modules\Admin\Queries;

use Modules\Catalog\Models\Product;

class ProductStatsQuery
{
    public function get(): object
    {
        return Product::toBase()
            ->selectRaw('COUNT(*) as total')
            ->selectRaw('COUNT(CASE WHEN stock <= low_stock_threshold AND stock > 0 THEN 1 END) as low_stock')
            ->selectRaw('COUNT(CASE WHEN stock = 0 THEN 1 END) as out_of_stock')
            ->first();
    }
}
