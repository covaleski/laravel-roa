<?php

namespace Covaleski\LaravelRoa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, \Covaleski\LaravelRoa\Resource\Resource> all() Get all mapped resources.
 * @method static void each(callable(\Covaleski\LaravelRoa\Resource\Resource $resource, string $name): void $callback): void Execute a callback over each resource.
 * @method static bool exists(string $name) Check whether a resource is mapped.
 * @method static \Covaleski\LaravelRoa\Resource\Resource get(string $name) Get a resource by its unique name.
 * @method static \Covaleski\LaravelRoa\Resource\Resource load(string $name) Load a resource by its unique name.
 *
 * @see \Covaleski\LaravelRoa\Resource\ResourceLoader
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
