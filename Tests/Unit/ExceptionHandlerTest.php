<?php

namespace Tests\Unit;

use Exception;
use Phox\Nebula\Atom\Implementation\Exceptions\BadCollectionType;
use Phox\Nebula\Atom\Implementation\Exceptions\CollectionHasKey;
use Phox\Nebula\Atom\TestCase;
use Phox\Nebula\EH\ExceptionHandlerProvider;
use Phox\Nebula\EH\Implementation\ExceptionHandler;
use stdClass;

class ExceptionHandlerTest extends TestCase
{
    /**
     * @throws CollectionHasKey
     * @throws BadCollectionType
     */
    protected function setUp(): void
    {
        parent::setUp();

        $this->nebula->addProvider(new ExceptionHandlerProvider());
    }

    public function testInstanceIsSingleton(): void
    {
        $this->assertInstanceOf(ExceptionHandler::class, $this->container()->get(ExceptionHandler::class));
        $this->assertIsSingleton(ExceptionHandler::class);
    }

    /**
     * @throws CollectionHasKey
     * @throws BadCollectionType
     */
    public function testListen(): void
    {
        $handler = $this->container()->get(ExceptionHandler::class);

        $testException = new class extends Exception {};
        $testExceptionClass = $testException::class;

        $handler->listen(fn(Exception $exception): mixed => $this->assertSame($testException, $exception), $testExceptionClass);

        $handler->execute($testException);
    }

    /**
     * @throws CollectionHasKey
     * @throws BadCollectionType
     */
    public function testReplacedHandlerInDI(): void
    {
        $handlerMock = $this->createMock(ExceptionHandler::class);
        $handlerMock->expects($this->once())->method('listen');
        $handlerMock->expects($this->once())->method('execute');


        $handler = $this->container()->get(ExceptionHandler::class);
        $handler->execute(new Exception());

        $this->container()->singleton($handlerMock, ExceptionHandler::class);

        $newHandler = $this->container()->get(ExceptionHandler::class);
        $newHandler->listen(fn(Exception $e) => $e);
        $newHandler->execute(new Exception());
    }
}
