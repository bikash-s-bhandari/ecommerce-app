<?php

namespace Database\Factories\Modules\Catalog\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
use Modules\Catalog\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<Category>
     */
    protected $model = Category::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = 'Category '.Str::random(6);

        return [
            'name'        => $name,
            'slug'        => Str::slug($name),
            'description' => 'Test category description',
            'parent_id'   => null,
            'image_path'  => null,
            'is_active'   => true,
            'sort_order'  => 0,
        ];
    }
}

