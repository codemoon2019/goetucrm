<?php

namespace Guesl\CardConnect\CoPilot\Facades;

use Guesl\CardConnect\CoPilot\Contracts\CoPilotFactory;
use Illuminate\Support\Facades\Facade;

class CoPilot extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return CoPilotFactory::class;
    }
}