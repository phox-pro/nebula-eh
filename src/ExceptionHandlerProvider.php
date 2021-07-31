<?php

namespace Phox\Nebula\EH;

use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\Notion\Abstracts\Provider;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\EH\Implementation\ExceptionHandler;
use Throwable;

class ExceptionHandlerProvider extends Provider
{
    public function __invoke(IDependencyInjection $dependencyInjection): void
    {
        $dependencyInjection->singleton(new ExceptionHandler());

        set_exception_handler(function (Throwable $throwable) {
            $container = Functions::container();
            $handler = $container->get(ExceptionHandler::class);

            $container->call([$handler, 'execute'], [$throwable]);
        });
    }
}