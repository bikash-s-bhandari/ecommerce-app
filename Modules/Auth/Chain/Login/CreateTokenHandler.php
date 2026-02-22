<?php

namespace Modules\Auth\Chain\Login;

/**
 * Terminal handler: revokes previous device token and issues a new one.
 */
class CreateTokenHandler extends AbstractLoginHandler
{
    public function handle(LoginContext $context): array
    {
        $user = $context->user;
        if (!$user) {
            throw new \RuntimeException('Login context has no user.');
        }

        $user->tokens()->where('name', $context->dto->deviceName)->delete();
        $token = $user->createToken($context->dto->deviceName)->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}
