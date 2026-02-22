<?php

namespace Modules\Auth\Actions;

use App\Exceptions\BusinessException;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class ResetPasswordAction
{
    public function execute(string $token, string $email, string $password): void
    {
        $status = Password::reset(
            ['email' => $email, 'password' => $password, 'token' => $token],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                    'remember_token' => Str::random(60),
                ])->save();
                $user->tokens()->delete(); // invalidate all tokens on reset
                event(new PasswordReset($user));
            }
        );
        if ($status !== Password::PASSWORD_RESET) {
            throw new BusinessException(__($status), 400);
        }
    }
}
