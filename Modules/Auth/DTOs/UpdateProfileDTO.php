<?php

namespace Modules\Auth\DTOs;

use Modules\Auth\Http\Requests\UpdateProfileRequest;

readonly class UpdateProfileDTO
{
    public function __construct(
        public string $name,
        public string $phone,
        public ?string $currentPassword,
        public ?string $newPassword,
    ) {}

    public static function fromRequest(UpdateProfileRequest $request): self
    {
        return new self(
            name: $request->validated('name'),
            phone: $request->validated('phone'),
            currentPassword: $request->validated('current_password'),
            newPassword: $request->validated('new_password'),
        );
    }
}
