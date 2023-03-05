<?php

namespace Phox\Nebula\EH;

use Phox\Nebula\Atom\Implementation\Services\ServiceContainerAccess;
use Phox\Nebula\Atom\Notion\IProvider;
use Phox\Nebula\EH\Implementation\ExceptionHandler;
use Phox\Nebula\EH\Notion\Interfaces\IExceptionHandler;
use Throwable;

class ExceptionHandlerProvider implements IProvider
{
    use ServiceContainerAccess;

    public function register(): void
    {
        $this->container()->singleton(ExceptionHandler::class, IExceptionHandler::class);

        set_exception_handler(
            fn(Throwable $throwable) => $this->container()->get(IExceptionHandler::class)
                ->execute($throwable)
        );
    }
}