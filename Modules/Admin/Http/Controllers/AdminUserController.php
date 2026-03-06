<?php

namespace Modules\Admin\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\Auth\Enums\UserStatusEnum;
use Modules\Auth\Http\Resources\UserResource;
use Modules\Auth\Repositories\UserRepositoryInterface;

class AdminUserController extends Controller
{
    public function __construct(private UserRepositoryInterface $userRepository) {}

    public function index(Request $request): JsonResponse
    {
        $users = $this->userRepository->findAll([
            'search' => $request->query('search'),
            'role' => $request->query('role'),
            'status' => $request->query('status'),
        ], (int) $request->query('per_page', 20));

        return $this->success(UserResource::collection($users)->response()->getData(true));
    }

    public function updateStatus(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'status' => ['required', Rule::enum(UserStatusEnum::class)],
        ]);

        $user = $this->userRepository->findById($id);

        $this->userRepository->update($user, ['status' => $request->validated('status')]);

        return $this->success(UserResource::make($user->fresh()), 'User status updated');
    }
}
