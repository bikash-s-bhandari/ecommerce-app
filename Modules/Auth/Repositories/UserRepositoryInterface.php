<?php

namespace Modules\Auth\Repositories;

use Modules\Auth\DTOs\RegisterDTO;
use Modules\Auth\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function findByEmail(string $email): ?User;
    public function findById(int $id): User;
    public function create(RegisterDTO $dto): User;
    public function update(User $user, array $data): User;
    public function findAll(array $filters = [], int $perPage = 15): LengthAwarePaginator;
}
