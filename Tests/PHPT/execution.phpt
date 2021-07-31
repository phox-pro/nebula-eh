--TEST--
ExceptionHandler::execute basic test
--FILE--
<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once 'vendor/autoload.php';

    $nebula = new \Phox\Nebula\Atom\Implementation\Application();
    $nebula->addProvider(new \Phox\Nebula\EH\ExceptionHandlerProvider());

    $handler = \Phox\Nebula\Atom\Implementation\Functions::container()
        ->get(\Phox\Nebula\EH\Implementation\ExceptionHandler::class);

    $handler->listen(function (Exception $exception) {
        echo $exception->getMessage();
    }, Exception::class);

    throw new Exception('Tested message here!');
?>
--EXPECT--
Tested message here!
