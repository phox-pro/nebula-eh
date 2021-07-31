--TEST--
ExceptionHandler::execute test with subclasses
--FILE--
<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once 'vendor/autoload.php';

    $nebula = new \Phox\Nebula\Atom\Implementation\Application();
    $nebula->addProvider(new \Phox\Nebula\EH\ExceptionHandlerProvider());

    $handler = \Phox\Nebula\Atom\Implementation\Functions::container()
        ->get(\Phox\Nebula\EH\Implementation\ExceptionHandler::class);

    $handler->listen(function (Throwable $throwable) {
        echo $throwable->getMessage() . ' from root!';
    });

    $handler->listen(function (LogicException $exception) {
        echo $exception->getMessage() . ' from here!';
    }, LogicException::class);

    $handler->listen(function (Exception $exception) {
        echo $exception->getMessage() . ' from subclass!';
    }, Exception::class);

    throw new LogicException('Tested message');
?>
--EXPECT--
Tested message from here!Tested message from root!Tested message from subclass!