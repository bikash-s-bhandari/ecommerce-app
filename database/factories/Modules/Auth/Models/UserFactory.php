<?php

namespace Database\Factories\Modules\Auth\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Modules\Auth\Enums\UserRoleEnum;
use Modules\Auth\Enums\UserStatusEnum;
use Modules\Auth\Models\User;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var class-string<User>
     */
    protected $model = User::class;

    /**
     * The current password being used by the factory.
     */
    protected static ?string $password = null;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name'              => $this->faker->name(),
            'email'             => $this->faker->unique()->safeEmail(),
            'email_verified_at' => now(),
            'password'          => static::$password ??= Hash::make('password'),
            'remember_token'    => Str::random(10),
            'role'              => UserRoleEnum::CUSTOMER,
            'status'            => UserStatusEnum::ACTIVE,
        ];
    }

    public function admin(): self
    {
        return $this->state(function () {
            return [
                'role' => UserRoleEnum::ADMIN,
                'status' => UserStatusEnum::ACTIVE,
            ];
        });
    }
}
