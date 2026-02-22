<?php

namespace Modules\Auth\Actions;

use Illuminate\Support\Facades\Password;
use Modules\Auth\Repositories\UserRepositoryInterface;

class ForgotPasswordAction
{
    public function execute(string $email): void
    {
        Password::sendResetLink(['email' => $email]);
    }
}
