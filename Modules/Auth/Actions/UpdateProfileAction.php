<?php

namespace Modules\Auth\Actions;

use Modules\Auth\Repositories\UserRepositoryInterface;
use Modules\Auth\Models\User;
use Modules\Auth\DTOs\UpdateProfileDTO;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\Hash;

class UpdateProfileAction
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
    ) {}
    public function execute(User $user, UpdateProfileDTO $dto): User
    {
        $data = array_filter([
            'name' => $dto->name,
            'phone' => $dto->phone,
        ]);
        if ($dto->newPassword) {
            if (!$dto->currentPassword || !Hash::check($dto->currentPassword, $user->password)) {
                throw new BusinessException('Current password is incorrect.', 422);
            }
            $data['password'] = $dto->newPassword;
        }
        return $this->userRepository->update($user, $data);
    }
}
