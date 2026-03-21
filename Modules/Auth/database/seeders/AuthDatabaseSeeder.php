<?php

namespace Modules\Auth\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Auth\Enums\UserRoleEnum;
use Modules\Auth\Enums\UserStatusEnum;
use Modules\Auth\Models\User;

class AuthDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name'   => 'Admin',
                'password' => 'admin123',
                'phone'  => null,
                'role'   => UserRoleEnum::ADMIN,
                'status' => UserStatusEnum::ACTIVE,
            ]
        );
    }
}
