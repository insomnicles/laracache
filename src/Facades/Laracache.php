<?php

namespace Insomnicles\Laracache\Facades;

use Illuminate\Support\Facades\Facade;

class Laracache extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laracache';
    }
}
