<?php

namespace Phox\Nebula\EH\Notion\Interfaces;

use Throwable;

interface IExceptionHandler
{
    public function execute(Throwable $throwable): void;

    /**
     * @param callable(Throwable): void $callback
     * @param class-string<Throwable> $exceptionClass
     * @return void
     */
    public function subscribe(callable $callback, string $exceptionClass = Throwable::class): void;
}