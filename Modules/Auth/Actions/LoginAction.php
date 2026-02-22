<?php

namespace Modules\Auth\Actions;

use Modules\Auth\Chain\Login\CreateTokenHandler;
use Modules\Auth\Chain\Login\InactiveStatusHandler;
use Modules\Auth\Chain\Login\LoginContext;
use Modules\Auth\Chain\Login\BannedStatusHandler;
use Modules\Auth\Chain\Login\ValidateCredentialsHandler;
use Modules\Auth\DTOs\LoginDTO;

class LoginAction
{
    private ValidateCredentialsHandler $chainHead;

    public function __construct(
        ValidateCredentialsHandler $validateCredentials,
        BannedStatusHandler $bannedStatus,
        InactiveStatusHandler $inactiveStatus,
        CreateTokenHandler $createToken,
    ) {
        $validateCredentials->setNext($bannedStatus);
        $bannedStatus->setNext($inactiveStatus);
        $inactiveStatus->setNext($createToken);

        $this->chainHead = $validateCredentials;
    }

    public function execute(LoginDTO $dto): array
    {
        $context = new LoginContext($dto);

        return $this->chainHead->handle($context);
    }
}
