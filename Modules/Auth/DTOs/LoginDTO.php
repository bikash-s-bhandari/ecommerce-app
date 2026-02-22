<?php

namespace Modules\Auth\DTOs;

use Modules\Auth\Http\Requests\LoginRequest;

readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public string $deviceName = 'web',
    ) {}

    public static function fromRequest(LoginRequest $request): self
    {
        return new self(
            email: $request->validated('email'),
            password: $request->validated('password'),
            deviceName: $request->validated('device_name', 'web'),
        );
    }
}
