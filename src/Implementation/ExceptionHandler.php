<?php

namespace Phox\Nebula\EH\Implementation;

use Phox\Nebula\Atom\Notion\Abstracts\Event;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Nebula\EH\Notion\Interfaces\IExceptionHandler;
use Phox\Structures\Abstracts\ObjectType;
use Phox\Structures\Abstracts\Type;
use Phox\Structures\AssociativeCollection;
use Phox\Structures\AssociativeObjectCollection;
use Phox\Structures\Collection;
use Throwable;

class ExceptionHandler implements IExceptionHandler
{
    /**
     * @var AssociativeObjectCollection<Collection<callable>>
     */
    protected AssociativeObjectCollection $handlers;

    public function __construct(protected IDependencyInjection $dependencyInjection)
    {
        $this->handlers = new AssociativeObjectCollection(
            ObjectType::fromClass(Collection::class)
        );

        $this->handlers->set(Throwable::class, new Collection(Type::CALLABLE));
    }

    public function execute(Throwable $throwable): void
    {
        $needKeys = array_filter($this->handlers->getKeys(), fn(string $key): bool => is_subclass_of($throwable, $key));

        if ($this->handlers->has($throwable::class) && !in_array($throwable::class, $needKeys)) {
            array_unshift($needKeys, $throwable::class);
        }

        $exceptionListeners = new Collection(Type::CALLABLE);

        foreach ($needKeys as $needKey) {
            $exceptionListeners->merge($this->handlers->get($needKey)->getItems());
        }

        foreach ($exceptionListeners as $exceptionListener) {
            $this->dependencyInjection->call($exceptionListener, [$throwable]);
        }
    }

    public function subscribe(callable $callback, string $exceptionClass = Throwable::class): void
    {
        if (!$this->handlers->has($exceptionClass)) {
            $this->handlers->set($exceptionClass, new Collection(Type::CALLABLE));
        }

        /**
         * @var Collection<callable> $exceptionHandlers
         */
        $exceptionHandlers = $this->handlers->get($exceptionClass);

        if (!$exceptionHandlers->contains($callback)) {
            $exceptionHandlers->add($callback);
        }
    }
}