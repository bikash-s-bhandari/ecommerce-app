<?php

namespace Modules\Auth\Enums;

enum UserRoleEnum: string
{
    case ADMIN    = 'admin';
    case CUSTOMER = 'customer';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN    => 'Administrator',
            self::CUSTOMER => 'Customer',
        };
    }

    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }
}
