<?php

namespace Database\Factories\Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Enums\ProductStatusEnum;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Product>
     */
    protected $model = Product::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = 'Product '.Str::random(6);

        return [
            'title'               => $title,
            'slug'                => Str::slug($title),
            'description'         => 'Test product description',
            'sku'                 => 'SKU-'.Str::upper(Str::random(8)),
            'price'               => 29.99,
            'sale_price'          => null,
            'stock'               => 10,
            'low_stock_threshold' => 2,
            'category_id'         => Category::factory(),
            'status'              => ProductStatusEnum::ACTIVE,
            'featured'            => false,
        ];
    }
}

