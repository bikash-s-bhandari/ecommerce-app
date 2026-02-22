<?php

namespace Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Modules\Auth\Actions\ForgotPasswordAction;
use Modules\Auth\Actions\LoginAction;
use Modules\Auth\Actions\RegisterUserAction;
use Modules\Auth\Actions\ResetPasswordAction;
use Modules\Auth\DTOs\LoginDTO;
use Modules\Auth\DTOs\RegisterDTO;
use Modules\Auth\Http\Requests\LoginRequest;
use Modules\Auth\Http\Requests\RegisterRequest;
use Modules\Auth\Http\Resources\UserResource;

class AuthController extends Controller
{
    public function register(RegisterRequest $request, RegisterUserAction $action): JsonResponse
    {
        $result = $action->execute(RegisterDTO::fromRequest($request));

        return $this->created(
            [
                'user' => UserResource::make($result['user']),
                'token' => $result['token'],
            ],
            'User registered successfully',
        );
    }

    public function login(LoginRequest $request, LoginAction $action): JsonResponse
    {
        $result = $action->execute(LoginDTO::fromRequest($request));

        return $this->success([
            'user' => UserResource::make($result['user']),
            'token' => $result['token'],
        ], 'Login successful');
    }

    public function me(Request $request): JsonResponse
    {
        return $this->success(UserResource::make($request->user()));
    }

    public function forgotPassword(Request $request, ForgotPasswordAction $action): JsonResponse
    {
        $request->validate(['email' => ['required', 'email']]);
        $action->execute($request->email);
        return $this->success(null, 'If that email exists, a reset link has been sent.');
    }

    public function resetPassword(Request $request, ResetPasswordAction $action): JsonResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'min:8', 'confirmed'],
        ]);
        $action->execute($request->token, $request->email, $request->password);
        return $this->success(null, 'Password reset successful. Please log in.');
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out successfully');
    }
}
