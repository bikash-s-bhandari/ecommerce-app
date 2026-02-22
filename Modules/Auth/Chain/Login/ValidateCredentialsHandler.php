<?php

namespace Modules\Auth\Chain\Login;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\Repositories\UserRepositoryInterface;

class ValidateCredentialsHandler extends AbstractLoginHandler
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}

    public function handle(LoginContext $context): array
    {
        $user = $this->userRepository->findByEmail($context->dto->email);

        if (!$user || !Hash::check($context->dto->password, $user->password)) {
            throw new BusinessException('Invalid credentials.', 401);
        }

        $context->user = $user;

        return $this->next($context);
    }
}
