<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Modules\Cart\Models\Cart;
use Modules\Cart\Models\CartItem;
use Modules\Catalog\Models\Category;
use Modules\Catalog\Models\Product;
use Modules\Payment\Contracts\PaymentGatewayInterface;
use Modules\Payment\DTOs\PaymentInitiateResultDTO;
use Modules\Payment\Factories\PaymentGatewayFactory;

uses(RefreshDatabase::class);

beforeEach(function () {
    // Mock the factory so no real gateway (Stripe/eSewa/Khalti) is called during tests.
    // The factory returns a fake gateway that always succeeds.
    $mockGateway = Mockery::mock(PaymentGatewayInterface::class);
    $mockGateway->shouldReceive('initiate')->andReturn(new PaymentInitiateResultDTO(
        type:         'intent',
        gatewayRef:   'pi_test_123',
        redirectUrl:  null,
        clientSecret: 'secret_test',
        formData:     [],
        raw:          [],
    ));

    $mockFactory = Mockery::mock(PaymentGatewayFactory::class);
    $mockFactory->shouldReceive('make')->andReturn($mockGateway);

    $this->app->instance(PaymentGatewayFactory::class, $mockFactory);
});

it('places an order successfully', function () {
    $user     = User::factory()->create();
    $category = Category::factory()->create();
    $product  = Product::factory()->create([
        'category_id' => $category->id,
        'stock'       => 10,
        'price'       => 25.00,
        'status'      => 'active',
    ]);
    $cart = Cart::create(['user_id' => $user->id]);
    CartItem::create(['cart_id' => $cart->id, 'product_id' => $product->id, 'quantity' => 2, 'unit_price' => 25.00]);

    $response = $this->actingAs($user)->postJson('/api/v1/orders', [
        'payment_gateway' => 'stripe',
        'payment_token'   => 'pm_test_card',
        'shipping_address' => [
            'full_name'   => 'John Doe',
            'street_1'    => '123 Main St',
            'city'        => 'New York',
            'state'       => 'NY',
            'postal_code' => '10001',
            'country'     => 'US',
        ],
    ]);

    $response->assertCreated()->assertJsonStructure(['data' => [
        'order_number',
        'total',
        'status',
    ]]);

    expect($product->fresh()->stock)->toBe(8);
    expect(CartItem::where('cart_id', $cart->id)->count())->toBe(0);
});

it('fails when cart is empty', function () {
    $user = User::factory()->create();
    Cart::create(['user_id' => $user->id]);

    $this->actingAs($user)->postJson('/api/v1/orders', [
        'payment_gateway' => 'stripe',
        'payment_token'   => 'pm_test',
        'shipping_address' => [
            'full_name'   => 'J',
            'street_1'    => 'S',
            'city'        => 'C',
            'state'       => 'S',
            'postal_code' => '1',
            'country'     => 'US',
        ],
    ])->assertStatus(422);
});

it('rejects an unsupported payment gateway', function () {
    $user = User::factory()->create();
    Cart::create(['user_id' => $user->id]);

    $this->actingAs($user)->postJson('/api/v1/orders', [
        'payment_gateway' => 'paypal',   // not in SUPPORTED list
        'payment_token'   => null,
        'shipping_address' => [
            'full_name'   => 'J',
            'street_1'    => 'S',
            'city'        => 'C',
            'state'       => 'S',
            'postal_code' => '1',
            'country'     => 'US',
        ],
    ])->assertStatus(422);
});
