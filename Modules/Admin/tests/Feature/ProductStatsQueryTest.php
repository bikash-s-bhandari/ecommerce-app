<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Admin\Queries\ProductStatsQuery;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

uses(RefreshDatabase::class);

function makeProduct(array $overrides = []): Product
{
    $category = Category::factory()->create();

    return Product::factory()->create(array_merge([
        'category_id'         => $category->id,
        'stock'               => 10,
        'low_stock_threshold' => 2,
    ], $overrides));
}

it('counts total products', function () {
    makeProduct();
    makeProduct();
    makeProduct();

    $stats = (new ProductStatsQuery)->get();

    expect((int) $stats->total)->toBe(3);
});

it('counts low stock products (stock > 0 and stock <= threshold)', function () {
    makeProduct(['stock' => 1, 'low_stock_threshold' => 2]);  // low stock
    makeProduct(['stock' => 2, 'low_stock_threshold' => 2]);  // low stock (at threshold)
    makeProduct(['stock' => 5, 'low_stock_threshold' => 2]);  // normal stock
    makeProduct(['stock' => 0, 'low_stock_threshold' => 2]);  // out of stock (not low)

    $stats = (new ProductStatsQuery)->get();

    expect((int) $stats->low_stock)->toBe(2);
});

it('counts out of stock products', function () {
    makeProduct(['stock' => 0]);
    makeProduct(['stock' => 0]);
    makeProduct(['stock' => 5]);

    $stats = (new ProductStatsQuery)->get();

    expect((int) $stats->out_of_stock)->toBe(2);
});

it('returns zero when no products exist', function () {
    $stats = (new ProductStatsQuery)->get();

    expect((int) $stats->total)->toBe(0)
        ->and((int) $stats->low_stock)->toBe(0)
        ->and((int) $stats->out_of_stock)->toBe(0);
});

it('does not count out of stock products as low stock', function () {
    makeProduct(['stock' => 0, 'low_stock_threshold' => 5]);

    $stats = (new ProductStatsQuery)->get();

    expect((int) $stats->low_stock)->toBe(0)
        ->and((int) $stats->out_of_stock)->toBe(1);
});
