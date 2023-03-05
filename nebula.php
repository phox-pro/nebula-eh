<?php

use Phox\Nebula\Atom\Notion\INebulaConfig;
use Phox\Nebula\Atom\Notion\IProvider;
use Phox\Nebula\EH\ExceptionHandlerProvider;

return new class implements INebulaConfig
{
    public function getProvider(): ?IProvider
    {
        return new ExceptionHandlerProvider();
    }
};