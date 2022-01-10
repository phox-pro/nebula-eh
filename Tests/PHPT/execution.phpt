--TEST--
ExceptionHandler::execute basic test
--FILE--
<?php
    error_reporting(E_ALL ^ E_DEPRECATED);
    require_once 'vendor/autoload.php';

    $nebula = new \Phox\Nebula\Atom\Implementation\Application();
    $dependencyContainer = $nebula->getDIContainer();
    $providersContainer = $dependencyContainer->get(\Phox\Nebula\Atom\Implementation\ProvidersContainer::class);
    $providersContainer->addProvider(new \Phox\Nebula\EH\ExceptionHandlerProvider());

    $handler = $dependencyContainer
        ->get(\Phox\Nebula\EH\Notion\Interfaces\IExceptionHandler::class);

    $handler->subscribe(function (Exception $exception) {
        echo $exception->getMessage();
    }, Exception::class);

    throw new Exception('Tested message here!');
?>
--EXPECT--
Tested message here!
