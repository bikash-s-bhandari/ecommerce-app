<?php

namespace Modules\Auth\Enums;

enum UserStatusEnum: string
{
    case ACTIVE   = 'active';
    case INACTIVE = 'inactive';
    case BANNED   = 'banned';

    public function isActive(): bool
    {
        return $this === self::ACTIVE;
    }
}
