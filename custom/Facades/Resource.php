<?php

namespace Covaleski\LaravelRoa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method array<string, \Covaleski\LaravelRoa\Resource\ResourceAccessor> all() Get all mapped resources.
 * @method void cache() Ensure map data is in storage.
 * @method void clear() Clear map data from memory and storage.
 * @method void compile() Compile map data to memory.
 * @method void delete() Delete map data from storage.
 * @method void each(callable $callback) Execute a callback over each mapped resource.
 * @method bool exists(string $name) Check whether a resource is mapped.
 * @method Covaleski\LaravelRoa\Resource\ResourceAccessor get(string $name) Get a resource by its name.
 * @method bool isCached() Check whether map data is in storage.
 * @method bool isLoaded() Check whether map data is loaded to memory.
 * @method void load() Load map data from storage.
 * @method void save() Save map data from memory to storage.
 * @method void unload() Clear map data from memory.
 * @method void wipe() Clear map and resource cache data from memory and storage.
 *
 * @uses Covaleski\LaravelRoa\Resource\ResourceMap to proxy its members.
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
        return \Covaleski\LaravelRoa\Resource\ResourceMap::class;
    }
}
