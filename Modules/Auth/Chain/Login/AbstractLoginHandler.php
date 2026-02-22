<?php

namespace Modules\Auth\Chain\Login;

abstract class AbstractLoginHandler implements LoginHandlerInterface
{
    protected ?LoginHandlerInterface $next = null;

    public function setNext(LoginHandlerInterface $next): LoginHandlerInterface
    {
        $this->next = $next;

        return $next;
    }

    protected function next(LoginContext $context): array
    {
        if ($this->next === null) {
            throw new \RuntimeException('End of login chain: no next handler.');
        }

        return $this->next->handle($context);
    }
}
