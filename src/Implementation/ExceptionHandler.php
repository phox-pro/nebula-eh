<?php

namespace Phox\Nebula\EH\Implementation;

use Phox\Nebula\Atom\Implementation\Functions;
use Phox\Nebula\Atom\Notion\Abstracts\Event;
use Phox\Nebula\Atom\Notion\Interfaces\IDependencyInjection;
use Phox\Structures\Collection;
use Throwable;

class ExceptionHandler extends Event
{
    protected Collection $listeners;
    protected IDependencyInjection $dependencyInjection;

    public function __construct()
    {
        parent::__construct();

        $this->listeners = new Collection(Collection::class);
        $this->listeners->set(Throwable::class, new Collection('callable'));

        $this->dependencyInjection = Functions::container()->get(IDependencyInjection::class);
    }

    public function execute(Throwable $throwable)
    {
        $needKeys = array_filter($this->listeners->getKeys(), fn(string $key): bool => is_subclass_of($throwable, $key));

        if ($this->listeners->has($throwable::class) && !in_array($throwable::class, $needKeys)) {
            array_unshift($needKeys, $throwable::class);
        }

        $exceptionListeners = new Collection('callable');

        foreach ($needKeys as $needKey) {
            $exceptionListeners->merge($this->listeners->get($needKey)->all());
        }

        foreach ($exceptionListeners as $exceptionListener) {
            $this->dependencyInjection->call($exceptionListener, [$throwable]);
        }
    }

    public function listen(callable $listener, string $exceptionClass = Throwable::class): void
    {
        $this->listeners->has($exceptionClass) ?: $this->listeners->set($exceptionClass, new Collection('callable'));

        /** @var Collection<callable> $exceptionListeners */
        $exceptionListeners = $this->listeners->get($exceptionClass);
        $exceptionListeners->contains($listener) ?: $exceptionListeners->add($listener);
    }
}