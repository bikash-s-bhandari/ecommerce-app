<?php

namespace Modules\Catalog\Actions;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Cache;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductImage;

class SetPrimaryImageAction
{
    public function execute(Product $product, ProductImage $image): void
    {
        if ($image->product_id !== $product->id) {
            throw new BusinessException('Image does not belong to this product.', 403);
        }

        // Demote current primary
        $product->images()->where('is_primary', true)->update(['is_primary' => false]);

        // Promote new primary
        $image->update(['is_primary' => true]);

        Cache::tags(['products', 'product:' . $product->id])->flush();
    }
}
