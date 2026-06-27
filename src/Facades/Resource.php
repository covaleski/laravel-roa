<?php

namespace Covaleski\Laravel\Catalog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array<int, \Covaleski\Laravel\Catalog\Resource\ResourceAccessor> all() Get all mapped resources.
 * @method static void cache() Ensure map data is in storage.
 * @method static void clear() Clear map data from memory and storage.
 * @method static void compile() Compile map data to memory.
 * @method static void delete() Delete map data from storage.
 * @method static void each(callable(\Covaleski\Laravel\Catalog\Resource\ResourceAccessor $resource, string $name): void $callback) Execute a callback over each mapped resource.
 * @method static bool exists(string $name) Check whether a resource is mapped.
 * @method static \Covaleski\Laravel\Catalog\Resource\ResourceAccessor get(string $name) Get a resource by its name.
 * @method static \Illuminate\Support\Collection<int, string> getDirectories() Get mapped directories.
 * @method static string getFilename() Get the map cache file's filename.
 * @method static int getSize() Get the map cache file's size.
 * @method static int getTimestamp() Get the map cache file's last modification time.
 * @method static bool isCached() Check whether map data is in storage.
 * @method static bool isLoaded() Check whether map data is loaded to memory.
 * @method static void load() Load map data from storage.
 * @method static void save() Save map data from memory to storage.
 * @method static void unload() Clear map data from memory.
 * @method static void wipe() Clear map and resource cache data from memory and storage.
 *
 * @uses Covaleski\Laravel\Catalog\Resource\ResourceMap to proxy its members.
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
        return \Covaleski\Laravel\Catalog\Resource\ResourceMap::class;
    }
}
