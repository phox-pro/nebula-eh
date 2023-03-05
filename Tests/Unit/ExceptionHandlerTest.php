<?php

namespace Tests\Unit;

use Exception;
use Phox\Nebula\Atom\Implementation\Application;
use Phox\Nebula\Atom\Implementation\Services\ServiceContainerAccess;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\EH\Implementation\ExceptionHandler;
use Phox\Nebula\EH\Notion\Interfaces\IExceptionHandler;

class ExceptionHandlerTest extends TestCase
{
    use ServiceContainerAccess;

    protected Application $app;

    protected function setUp(): void
    {
        parent::setUp();

        $this->app = new Application();
    }

    public function testInstanceIsSingleton(): void
    {
        $this->app->run();

        $this->assertInstanceOf(ExceptionHandler::class, $this->container()->get(IExceptionHandler::class));
        $this->assertSame(
            $this->container()->get(IExceptionHandler::class),
            $this->container()->get(IExceptionHandler::class)
        );
    }

    public function testSubscribe(): void
    {
        $this->app->run();

        $handler = $this->container()->get(IExceptionHandler::class);

        $testException = new class extends Exception {};

        $handler->subscribe(fn(Exception $exception): mixed => $this->assertSame($testException, $exception), $testException::class);

        $handler->execute($testException);
    }

    public function testReplacedHandlerInDI(): void
    {
        $handlerMock = $this->createMock(ExceptionHandler::class);
        $handlerMock->expects($this->once())->method('subscribe');
        $handlerMock->expects($this->once())->method('execute');


        $handler = $this->container()->get(ExceptionHandler::class);
        $handler->execute(new Exception());

        $this->container()->singleton($handlerMock, ExceptionHandler::class);

        $newHandler = $this->container()->get(ExceptionHandler::class);
        $newHandler->subscribe(fn(Exception $e) => $e);
        $newHandler->execute(new Exception());
    }
}
