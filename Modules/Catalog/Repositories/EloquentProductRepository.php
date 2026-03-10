<?php

namespace Modules\Catalog\Repositories;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\Catalog\DTOs\ProductDTO;
use Modules\Catalog\DTOs\ProductFilterDTO;
use Modules\Catalog\Filters\{CategoryFilter, PriceRangeFilter, SearchFilter, TagFilter};
use Modules\Catalog\Models\Product;

class EloquentProductRepository implements ProductRepositoryInterface
{
    public function __construct(protected Product $model) {}

    public function findById(int $id): Product
    {
        return $this->model->with(['category', 'images', 'tags'])->findOrFail($id);
    }
    public function findBySlug(string $slug): Product
    {
        return $this->model->with(['category', 'images', 'tags'])->where('slug', $slug)->firstOrFail();
    }

    public function search(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $filters = [
            new SearchFilter($filter),
            new CategoryFilter($filter),
            new PriceRangeFilter($filter),
            new TagFilter($filter),
        ];
        $query = $this->model->visible()->with(['category', 'images', 'tags']);

        foreach ($filters as $f) {
            $f->apply($query);
        }
        $allowedSorts = ['price', 'created_at', 'title'];

        $sortBy = in_array($filter->sortBy, $allowedSorts) ? $filter->sortBy :
            'created_at';

        $query->orderBy($sortBy, $filter->sortDir === 'asc' ? 'asc' : 'desc');

        return $query->paginate(min($filter->perPage, 50));
    }

    public function create(ProductDTO $dto): Product
    {
        $product = $this->model->create([
            'title' => $dto->title,
            'description' => $dto->description,
            'price' => $dto->price,
            'sale_price' => $dto->salePrice,
            'stock' => $dto->stock,
            'sku' => $dto->sku,
            'category_id' => $dto->categoryId,
            'status' => $dto->status,
            'featured' => $dto->featured,
        ]);

        if ($dto->tagIds) {
            $product->tags()->sync($dto->tagIds);
        }

        // Naya product create bhayo → sabai cached product lists remove (if store supports tags)
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['products'])->flush();
        }

        return $product->load(['category', 'images', 'tags']);
    }

    public function update(Product $product, ProductDTO $dto): Product
    {
        $product->update([
            'title' => $dto->title,
            'description' => $dto->description,
            'price' => $dto->price,
            'sale_price' => $dto->salePrice,
            'stock' => $dto->stock,
            'sku' => $dto->sku,
            'category_id' => $dto->categoryId,
            'status' => $dto->status,
            'featured' => $dto->featured,
        ]);
        $product->tags()->sync($dto->tagIds);

        // Product update bhayo → product list cache + specific product cache clear garnu parcha,
        // kina ki old data purano huncha. Only when store supports tags.
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['products', 'product:' . $product->id])->flush();
        }

        return $product->load(['category', 'images', 'tags']);
    }

    public function delete(Product $product): void
    {
        $product->delete();

        // Product delete bhayo → list cache clear, so next time query fresh data lincha.
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['products'])->flush();
        }
    }
    public function decrementStock(int $productId, int $qty): void
    {
        $this->model->where('id', $productId)->decrement('stock', $qty);

        // Stock change bhayo → specific product cache refresh garna parcha, jaise "in stock" display update huncha.
        if (Cache::getStore() instanceof TaggableStore) {
            Cache::tags(['product:' . $productId])->flush();
        }
    }
}
