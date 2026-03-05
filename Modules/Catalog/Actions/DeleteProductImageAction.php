<?php

namespace Modules\Catalog\Actions;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;
use Modules\Catalog\Models\ProductImage;

class DeleteProductImageAction
{
    public function execute(ProductImage $image): void
    {
        Storage::disk('public')->delete($image->path);

        $productId = $image->product_id;
        $wasPrimary = $image->is_primary;

        $image->delete();

        // If deleted image was primary, promote the next image
        if ($wasPrimary) {
            ProductImage::where('product_id', $productId)
                ->orderBy('sort_order')
                ->first()
                ?->update(['is_primary' => true]);
        }

        Cache::tags(['products', 'product:' . $productId])->flush();
    }
}
