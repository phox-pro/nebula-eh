<?php

namespace Tests\Unit;

use Exception;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Exceptions\CollectionHasKey;
use Phox\Nebula\Atom\Implementation\ProvidersContainer;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\EH\ExceptionHandlerProvider;
use Phox\Nebula\EH\Implementation\ExceptionHandler;
use Phox\Nebula\EH\Notion\Interfaces\IExceptionHandler;
use stdClass;

class ExceptionHandlerTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $providersContainer = $this->container()->get(ProvidersContainer::class);
        $providersContainer->addProvider(new ExceptionHandlerProvider());
    }

    public function testInstanceIsSingleton(): void
    {
        $this->assertInstanceOf(ExceptionHandler::class, $this->container()->get(IExceptionHandler::class));
        $this->assertIsSingleton(IExceptionHandler::class);
    }

    public function testSubscribe(): void
    {
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
