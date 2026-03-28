<?php

namespace Modules\Catalog\Actions;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Cache;
use Modules\Catalog\Models\Product;
use Modules\Catalog\Models\ProductImage;

class UploadProductImagesAction
{
    public function execute(Product $product, array $files, ?int $primaryIndex = null): Product
    {
        // If no images exist yet, first upload is automatically primary
        $hasExistingPrimary = $product->images()->where('is_primary', true)->exists();

        foreach ($files as $index => $file) {
            /** @var UploadedFile $file */
            $path = $file->store("products/{$product->id}", 'public');

            $isPrimary = !$hasExistingPrimary && $index === ($primaryIndex ?? 0);

            // If this one is primary, demote all existing primaries first
            if ($isPrimary) {
                $product->images()->where('is_primary', true)->update(['is_primary' => false]);
            }

            ProductImage::create([
                'product_id' => $product->id,
                'path'       => $path,
                'alt_text'   => $product->title,
                'is_primary' => $isPrimary,
                'sort_order' => $product->images()->max('sort_order') + $index + 1,
            ]);

            // After first image is set as primary, rest are not
            if ($isPrimary) {
                $hasExistingPrimary = true;
            }
        }

        //tags cache driver redis मा support garcha
       Cache::tags(['products', 'product:' . $product->id])->flush();
    //    Cache::forget('products');
    //    Cache::forget('product:' . $product->id);

        return $product->load(['images']);
    }
}
