<?php

namespace Modules\Auth\Chain\Login;

/**
 * Chain of Responsibility: each handler validates or performs a step.
 * Returns ['user' => User, 'token' => string] when login succeeds.
 */
interface LoginHandlerInterface
{
    public function setNext(LoginHandlerInterface $next): LoginHandlerInterface;

    /**
     * @return array{user: \Modules\Auth\Models\User, token: string}
     */
    public function handle(LoginContext $context): array;
}
