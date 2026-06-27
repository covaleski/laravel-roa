<?php

namespace Covaleski\Laravel\Catalog\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static array<int, \Covaleski\Laravel\Catalog\Model\ModelAccessor> all() Get all mapped models.
 * @method static void cache() Ensure map data is in storage.
 * @method static void clear() Clear map data from memory and storage.
 * @method static void compile() Compile map data to memory.
 * @method static void delete() Delete map data from storage.
 * @method static void each(callable(\Covaleski\Laravel\Catalog\Model\ModelAccessor $model, string $name): void $callback) Execute a callback over each mapped model.
 * @method static bool exists(string $name) Check whether a model is mapped.
 * @method static \Covaleski\Laravel\Catalog\Model\ModelAccessor get(string $name) Get a model by its cache name.
 * @method static \Illuminate\Support\Collection<int, string> getDirectories() Get mapped directories.
 * @method static string getFilename() Get the map cache file's filename.
 * @method static int getSize() Get the map cache file's size.
 * @method static int getTimestamp() Get the map cache file's last modification time.
 * @method static bool isCached() Check whether map data is in storage.
 * @method static bool isLoaded() Check whether map data is loaded to memory.
 * @method static void load() Load map data from storage.
 * @method static void save() Save map data from memory to storage.
 * @method static void unload() Clear map data from memory.
 * @method static void wipe() Clear model map and cache data from memory and storage.
 *
 * @uses Covaleski\Laravel\Catalog\Model\ModelMap to proxy its members.
 */
class Catalog extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return \Covaleski\Laravel\Catalog\Model\ModelMap::class;
    }
}
