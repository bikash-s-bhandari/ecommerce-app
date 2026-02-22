<?php

namespace Modules\Auth\Actions;

use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Hash;
use Modules\Auth\DTOs\LoginDTO;
use Modules\Auth\Enums\UserStatusEnum;
use Modules\Auth\Repositories\UserRepositoryInterface;

class LoginAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}
    public function execute(LoginDTO $dto): array
    {
        $user = $this->userRepository->findByEmail($dto->email);
        if (!$user || !Hash::check($dto->password, $user->password)) {
            throw new BusinessException('Invalid credentials.', 401);
        }
        if ($user->status === UserStatusEnum::BANNED) {
            throw new BusinessException('Account is banned.', 403);
        }
        if ($user->status === UserStatusEnum::INACTIVE) {
            throw new BusinessException('Account is inactive.', 403);
        }
        $user->tokens()->where('name', $dto->deviceName)->delete(); // revoke old
        $token = $user->createToken($dto->deviceName)->plainTextToken;
        return ['user' => $user, 'token' => $token];
    }
}
