<?php

namespace Phox\Nebula\EH;

use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\EH\Implementation\ExceptionHandler;
use Phox\Nebula\EH\Notion\Interfaces\IExceptionHandler;
use Throwable;

class ExceptionHandlerProvider extends Provider
{
    public function __invoke(IDependencyInjection $dependencyInjection): void
    {
        $dependencyInjection->singleton(ExceptionHandler::class, IExceptionHandler::class);

        set_exception_handler(function (Throwable $throwable) use ($dependencyInjection) {
            $container = $dependencyInjection->get(IDependencyInjection::class);
            $handler = $container->get(IExceptionHandler::class);

            $container->call([$handler, 'execute'], [$throwable]);
        });
    }
}