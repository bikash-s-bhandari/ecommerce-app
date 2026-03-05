<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Modules\Catalog\Actions\DeleteProductImageAction;
use Modules\Catalog\Actions\SetPrimaryImageAction;
use Modules\Catalog\Actions\UploadProductImagesAction;
use Modules\Catalog\Http\Requests\UploadProductImagesRequest;
use Modules\Catalog\Http\Resources\ProductImageResource;
use Modules\Catalog\Models\ProductImage;
use Modules\Catalog\Repositories\ProductRepositoryInterface;

class ProductImageController extends Controller
{
    public function upload(
        UploadProductImagesRequest $request,
        int $id,
        ProductRepositoryInterface $repo,
        UploadProductImagesAction $action
    ): JsonResponse {
        $product = $repo->findById($id);

        $result = $action->execute(
            $product,
            $request->file('images'),
            $request->validated('primary_index')
        );

        return $this->success(
            ProductImageResource::collection($result->images),
            'Images uploaded successfully'
        );
    }

    public function setPrimary(
        int $productId,
        ProductImage $image,
        ProductRepositoryInterface $repo,
        SetPrimaryImageAction $action
    ): JsonResponse {
        $product = $repo->findById($productId);
        $action->execute($product, $image);

        return $this->success(null, 'Primary image updated');
    }

    public function destroy(
        ProductImage $image,
        DeleteProductImageAction $action
    ): JsonResponse {
        // Only admin (already guarded by route middleware)
        $action->execute($image);

        return $this->noContent();
    }
}
