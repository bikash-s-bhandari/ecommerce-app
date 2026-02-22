<?php

namespace Modules\Auth\Actions;

use App\Exceptions\BusinessException;
use Modules\Auth\DTOs\RegisterDTO;
use Modules\Auth\Repositories\UserRepositoryInterface;

class RegisterUserAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}
    public function execute(RegisterDTO $dto): array
    {
        if ($this->userRepository->findByEmail($dto->email)) {
            throw new BusinessException('Email already registered.', 409);
        }
        $user = $this->userRepository->create($dto);

        $token = $user->createToken('web')->plainTextToken;

        return ['user' => $user, 'token' => $token];
    }
}
