<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;

uses(RefreshDatabase::class);
it('returns paginated product list', function () {
    $category = Category::factory()->create();
    Product::factory()->count(5)->create(['category_id' => $category->id, 'status' =>
    'active']);
    $this->getJson('/api/v1/products')
        ->assertOk()
        ->assertJsonStructure(['data' => ['data', 'meta']]);
});
it('filters products by category', function () {
    $cat1 = Category::factory()->create();
    $cat2 = Category::factory()->create();
    Product::factory()->create(['category_id' => $cat1->id, 'status' => 'active', 'title'
    => 'Cat1 Product']);
    Product::factory()->create(['category_id' => $cat2->id, 'status' => 'active', 'title'
    => 'Cat2 Product']);
    $response = $this->getJson('/api/v1/products?category_id=' . $cat1->id);
    $response->assertOk();
    expect($response->json('data.data'))->toHaveCount(1);
    expect($response->json('data.data.0.title'))->toBe('Cat1 Product');
});
it('allows admin to create a product', function () {
    $admin = User::factory()->admin()->create();
    $category = Category::factory()->create();
    $this->actingAs($admin)->postJson('/api/v1/products', [
        'title' => 'New Product',
        'description' => 'Product description',
        'price' => 29.99,
        'stock' => 100,
        'category_id' => $category->id,
        'status' => 'active',
    ])->assertCreated()->assertJsonPath('data.title', 'New Product');
});
it('forbids customers from creating products', function () {
    $customer = User::factory()->create();
    $category = Category::factory()->create();
    $this->actingAs($customer)->postJson('/api/v1/products', [
        'title' => 'Hack',
        'description' => '...',
        'price' => 1,
        'stock' => 1,
        'category_id' => $category->id,
        'status' => 'active',
    ])->assertForbidden();
});
