<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Payment\Contracts\PaymentGatewayInterface;

uses(RefreshDatabase::class);
beforeEach(function () {
    // Mock Stripe gateway
    $this->app->bind(PaymentGatewayInterface::class, function () {
        $mock = Mockery::mock(PaymentGatewayInterface::class);
        $mock->shouldReceive('createIntent')->andReturn([
            'id' => 'pi_test_123',
            'status' => 'succeeded',
            'client_secret' => 'secret',
            'raw' => [],
        ]);
        return $mock;
    });
});
it('places an order successfully', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->create([
        'category_id' => $category->id,
        'stock' => 10,
        'price' => 25.00,
        'status' => 'active'
    ]);
    $cart = Cart::create(['user_id' => $user->id]);
    CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' =>
    2, 'unit_price' => 25.00]);
    $response = $this->actingAs($user)->postJson('/api/v1/orders', [
        'payment_token' => 'pm_test_card',
        'shipping_address' => [
            'full_name' => 'John Doe',
            'street_1' => '123 Main St',
            'city' => 'New York',
            'state' => 'NY',
            'postal_code' => '10001',
            'country' => 'US',
        ],
    ]);
    $response->assertCreated()->assertJsonStructure(['data' => [
        'order_number',
        'total',
        'status'
    ]]);
    // Stock should be decremented
    expect($product->fresh()->stock)->toBe(8);
    // Cart should be cleared
    expect(CartItem::where('cart_id', $cart->id)->count())->toBe(0);
});
it('fails when cart is empty', function () {
    $user = User::factory()->create();
    Cart::create(['user_id' => $user->id]);

    $this->actingAs($user)->postJson('/api/v1/orders', [
        'payment_token' => 'pm_test',
        'shipping_address' => [
            'full_name' => 'J',
            'street_1' => 'S',
            'city' => 'C',
            'state' => 'S',
            'postal_code' => '1',
            'country' => 'US'
        ],
    ])->assertStatus(422);
});
