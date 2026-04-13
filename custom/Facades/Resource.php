<?php

namespace Covaleski\LaravelRoa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Covaleski\LaravelRoa\Resource\ResourceService
 */
class Resource extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Covaleski\LaravelRoa\Resource\ResourceLoader::class;
    }
}
