<?php

namespace Modules\Auth\Chain\Login;

use App\Exceptions\BusinessException;
use Modules\Auth\Enums\UserStatusEnum;

class BannedStatusHandler extends AbstractLoginHandler
{
    public function handle(LoginContext $context): array
    {
        if ($context->user?->status === UserStatusEnum::BANNED) {
            throw new BusinessException('Account is banned.', 403);
        }

        return $this->next($context);
    }
}
