<?php

namespace Modules\Catalog\Actions;

use Illuminate\Cache\TaggableStore;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Modules\Catalog\DTOs\ProductFilterDTO;
use Modules\Catalog\Repositories\ProductRepositoryInterface;

class ListProductsAction
{
    public function __construct(
        private ProductRepositoryInterface $productRepository,
    ) {}

    public function execute(ProductFilterDTO $filter): LengthAwarePaginator
    {
        $cacheKey = 'products:' . md5(serialize($filter));

        // Use cache tags only if the current store supports them. Otherwise, bypass caching.
        if (Cache::getStore() instanceof TaggableStore) {
            return Cache::tags(['products'])->remember($cacheKey, 900, function () use ($filter) {
                return $this->productRepository->search($filter);
            });
        }

        return $this->productRepository->search($filter);
    }
}
