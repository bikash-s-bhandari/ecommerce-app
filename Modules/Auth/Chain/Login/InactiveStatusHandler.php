<?php

namespace Modules\Auth\Chain\Login;

use App\Exceptions\BusinessException;
use Modules\Auth\Enums\UserStatusEnum;

class InactiveStatusHandler extends AbstractLoginHandler
{
    public function handle(LoginContext $context): array
    {
        if ($context->user?->status === UserStatusEnum::INACTIVE) {
            throw new BusinessException('Account is inactive.', 403);
        }

        return $this->next($context);
    }
}
