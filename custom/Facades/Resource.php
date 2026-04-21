<?php

namespace Covaleski\LaravelRoa\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array<string, \Covaleski\LaravelRoa\Resource\ResourceAccessor> all() Get all mapped resources.
 * @method static void cache() Ensure map data is in storage.
 * @method static void clear() Clear map data from memory and storage.
 * @method static void compile() Compile map data to memory.
 * @method static void delete() Delete map data from storage.
 * @method static void each(callable(ResourceAccessor $resource, string $name): void $callback) Execute a callback over each mapped resource.
 * @method static bool exists(string $name) Check whether a resource is mapped.
 * @method static \Covaleski\LaravelRoa\Resource\ResourceAccessor get(string $name) Get a resource by its name.
 * @method static bool isCached() Check whether map data is in storage.
 * @method static bool isLoaded() Check whether map data is loaded to memory.
 * @method static void load() Load map data from storage.
 * @method static void save() Save map data from memory to storage.
 * @method static void unload() Clear map data from memory.
 * @method static void wipe() Clear map and resource cache data from memory and storage.
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
