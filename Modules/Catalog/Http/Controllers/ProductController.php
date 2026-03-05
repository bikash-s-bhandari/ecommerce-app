<?php

namespace Modules\Catalog\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Catalog\Actions\CreateProductAction;
use Modules\Catalog\Actions\ListProductsAction;
use Modules\Catalog\Actions\UpdateProductAction;
use Modules\Catalog\DTOs\ProductDTO;
use Modules\Catalog\DTOs\ProductFilterDTO;
use Modules\Catalog\Http\Requests\StoreProductRequest;
use Modules\Catalog\Http\Requests\UpdateProductRequest;
use Modules\Catalog\Http\Resources\ProductResource;
use Modules\Catalog\Repositories\ProductRepositoryInterface;

class ProductController extends Controller
{
    public function index(Request $request, ListProductsAction $action): JsonResponse
    {
        $filter = ProductFilterDTO::fromRequest($request);
        $products = $action->execute($filter);

        return $this->success(ProductResource::collection($products)->response()->getData(true));
    }
    public function show(string $slug, ProductRepositoryInterface $repo): JsonResponse
    {
        $product = $repo->findBySlug($slug);

        return $this->success(ProductResource::make($product));
    }

    public function store(StoreProductRequest $request, CreateProductAction $action): JsonResponse
    {
        $product = $action->execute(ProductDTO::fromRequest($request));

        return $this->created(ProductResource::make($product), 'Product created');
    }
    public function update(UpdateProductRequest $request, int $id, UpdateProductAction $action, ProductRepositoryInterface $repo): JsonResponse
    {
        $product = $repo->findById($id);
        $product = $action->execute($product, ProductDTO::fromRequest($request));

        return $this->success(ProductResource::make($product), 'Product updated');
    }

    public function destroy(int $id, ProductRepositoryInterface $repo): JsonResponse
    {
        $repo->delete($repo->findById($id));

        return $this->noContent();
    }
}
