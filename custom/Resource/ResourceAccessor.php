<?php

namespace Covaleski\LaravelRoa\Resource;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class ResourceAccessor
{
    /**
     * Model compiler.
     */
    protected ModelCompiler $modelCompiler;

    /**
     * Resource cache file path.
     */
    protected string $path;

    /**
     * Resource cache.
     */
    protected ResourceCache $resourceCache;

    /**
     * Invoke an inaccessible method.
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->get(), $name], $arguments);
    }

    /**
     * Create the resource accessor instance.
     *
     * @param class-string<Model> $model
     */
    public function __construct(
        /**
         * Resource name.
         */
        public string $name,

        /**
         * Model class name.
         *
         * @var class-string<Model>
         */
        public string $model,

        /**
         * Filesystem disk instance.
         */
        protected Filesystem $disk,
    ) {
        $this->modelCompiler = new ModelCompiler($this->model);
        $this->path = "{$this->name}.cache";
    }

    /**
     * Read data from an inaccessible property.
     */
    public function __get(string $name)
    {
        return $this->get()->$name;
    }

    /**
     * Set data to an inaccessible property.
     */
    public function __set(string $name, mixed $value)
    {
        $this->get()->$name = $value;
    }

    /**
     * Ensure resource cache data is in storage.
     *
     * If not in memory, also compiles the resource cache.
     */
    public function cache(): void
    {
        if (!$this->isCached()) {
            if (!$this->isLoaded()) {
                $this->compile();
            }
            $this->save();
        }
    }

    /**
     * Clear resource cache data from memory and storage.
     */
    public function clear(): void
    {
        $this->unload();
        $this->delete();
    }

    /**
     * Compile resource cache data to memory.
     */
    public function compile(): void
    {
        $this->resourceCache = $this->modelCompiler->compile();
    }

    /**
     * Delete resource cache data from storage.
     */
    public function delete(): void
    {
        if ($this->disk->exists($this->path)) {
            $this->disk->delete($this->path);
        }
    }

    /**
     * Get the resource cache.
     *
     * If not cached in memory, loads the resource cache file.
     *
     * If the not cached in storage, compiles the resource cache.
     */
    public function get(): ResourceCache
    {
        if (!$this->isLoaded()) {
            $this->cache();
            $this->load();
        }
        return $this->resourceCache;
    }

    /**
     * Get the resource cache data file size in storage.
     */
    public function getSize(): ?int
    {
        return $this->isCached() ? $this->disk->size($this->path) : null;
    }

    /**
     * Check whether resource cache data is in storage.
     */
    public function isCached(): bool
    {
        return $this->disk->exists($this->path);
    }

    /**
     * Check whether resource cache data is loaded to memory.
     */
    public function isLoaded(): bool
    {
        return isset($this->resourceCache);
    }

    /**
     * Load map resource cache data from storage.
     */
    public function load(): void
    {
        if (!$this->isCached()) {
            throw new RuntimeException('Resource is not cached.');
        }
        $this->resourceCache = $this->parse($this->disk->get($this->path));
    }

    /**
     * Save resource cache data from memory to storage.
     */
    public function save(): void
    {
        if (!$this->isLoaded()) {
            throw new RuntimeException('Resource cache is not set.');
        }
        $this->disk->put($this->path, $this->unparse($this->resourceCache));
    }

    /**
     * Clear resource cache data from memory.
     */
    public function unload(): void
    {
        unset($this->resourceCache);
    }

    /**
     * Parse serialized data into a `ResourceCache` instance.
     */
    protected function parse(string $data): ResourceCache
    {
        return unserialize($data);
    }

    /**
     * Turn a `ResourceCache` instance into serialized data.
     */
    protected function unparse(ResourceCache $data): string
    {
        return serialize($data);
    }
}
