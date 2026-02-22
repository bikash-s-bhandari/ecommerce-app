<?php

namespace Modules\Auth\Chain\Login;

use Modules\Auth\DTOs\LoginDTO;
use Modules\Auth\Models\User;

/**
 * Context object passed through the login chain of responsibility.
 * Handlers can set the authenticated user; the final handler returns the token result.
 */
class LoginContext
{
    public ?User $user = null;

    public function __construct(
        public readonly LoginDTO $dto,
    ) {}
}
