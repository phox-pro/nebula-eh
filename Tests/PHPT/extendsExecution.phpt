--TEST--
ExceptionHandler::execute test with subclasses
--FILE--
<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once 'vendor/autoload.php';

    $nebula = new \Phox\Nebula\Atom\Implementation\Application();
    $dependencyContainer = \Phox\Nebula\Atom\Implementation\Services\ServiceContainerFacade::instance();

    $nebula->run();

    $handler = $dependencyContainer
        ->get(\Phox\Nebula\EH\Notion\Interfaces\IExceptionHandler::class);

    $handler->subscribe(function (Throwable $throwable) {
        echo $throwable->getMessage() . ' from root!';
    });

    $handler->subscribe(function (LogicException $exception) {
        echo $exception->getMessage() . ' from here!';
    }, LogicException::class);

    $handler->subscribe(function (Exception $exception) {
        echo $exception->getMessage() . ' from subclass!';
    }, Exception::class);

    throw new LogicException('Tested message');
?>
--EXPECT--
Tested message from here!Tested message from root!Tested message from subclass!