<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Auth\Models\User;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);
it('registers a new user', function () {
    $response = $this->postJson('/api/v1/auth/register', [
        'name' => 'John Doe',
        'email' => 'john@gmail.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ]);
    $response->assertCreated()
        ->assertJsonStructure(['data' => ['user', 'token']]);
    $this->assertDatabaseHas('users', ['email' => 'john@gmail.com']);
});
it('returns 409 if email already registered', function () {
    User::factory()->create(['email' => 'john@gmail.com']);
    $this->postJson('/api/v1/auth/register', [
        'name' => 'John',
        'email' => 'john@gmail.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ])->assertStatus(409);
});
it('logs in with valid credentials', function () {
    User::factory()->create(['email' => 'john@gmail.com', 'password' =>
    bcrypt('secret123')]);
    $this->postJson('/api/v1/auth/login', ['email' => 'john@gmail.com', 'password' =>
    'secret123'])
        ->assertOk()
        ->assertJsonStructure(['data' => ['token']]);
});
it('returns 401 with invalid credentials', function () {
    User::factory()->create(['email' => 'john@gmail.com', 'password' =>
    bcrypt('correct')]);
    $this->postJson('/api/v1/auth/login', ['email' => 'john@gmail.com', 'password' =>
    'wrong'])
        ->assertStatus(401);
});
it('returns current user for authenticated requests', function () {
    $user = User::factory()->create();
    $this->actingAs($user)
        ->getJson('/api/v1/auth/me')
        ->assertOk()
        ->assertJsonPath('data.email', $user->email);
});
