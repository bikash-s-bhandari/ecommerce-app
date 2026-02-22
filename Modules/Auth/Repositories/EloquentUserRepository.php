<?php

namespace Modules\Auth\Repositories;

use Modules\Auth\DTOs\RegisterDTO;
use Modules\Auth\Enums\UserRoleEnum;
use Modules\Auth\Enums\UserStatusEnum;
use Modules\Auth\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class EloquentUserRepository implements UserRepositoryInterface
{

    public function __construct(protected User $model) {}
    public function findByEmail(string $email): ?User
    {
        return $this->model->where('email', $email)->first();
    }
    public function findById(int $id): User
    {
        return $this->model->findOrFail($id);
    }
    public function create(RegisterDTO $dto): User
    {
        return $this->model->create([
            'name' => $dto->name,
            'email' => $dto->email,
            'password' => $dto->password,
            'phone' => $dto->phone,
            'role' => UserRoleEnum::CUSTOMER,
            'status' => UserStatusEnum::ACTIVE,
        ]);
    }
    public function update(User $user, array $data): User
    {
        $user->update(array_filter($data, fn($v) => !is_null($v)));
        return $user->fresh();
    }

    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->query()
            ->when(isset($filters['role']), fn($q) => $q->where('role', $filters['role']))
            ->when(isset($filters['status']), fn($q) => $q->where(
                'status',
                $filters['status']
            ))
            ->when(isset($filters['search']), fn($q) => $q->where(
                'name',
                'like',
                "%{$filters['search']}%"
            )
                ->orWhere('email', 'like', "%{$filters['search']}%"))
            ->latest()
            ->paginate($perPage);
    }
}
