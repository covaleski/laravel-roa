<?php

namespace Covaleski\Laravel\Catalog\Model;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * @property array<int, \Covaleski\Laravel\Catalog\Interfaces\ModelAttributeInterface> $attributes Attributes.
 * @property class-string<\Illuminate\Database\Eloquent\Model> $model Model class name.
 * @property string $name Model cache unique snake-case name.
 * @method ?\Covaleski\Laravel\Catalog\Model\TAttribute getAttribute(string $type) Get the first attribute of the specified class name.
 * @method array<int, \Covaleski\Laravel\Catalog\Model\TAttribute> getAttributes(string $type) Get all attributes of the specified class name.
 *
 * @uses Covaleski\Laravel\Catalog\Model\ModelCache to proxy its members.
 */
class ModelAccessor
{
    /**
     * Filesystem disk instance.
     */
    protected Filesystem $disk;

    /**
     * Model compiler.
     */
    protected ModelCompiler $modelCompiler;

    /**
     * Model cache file path.
     */
    protected string $path;

    /**
     * Model cache.
     */
    protected ModelCache $modelCache;

    /**
     * Model parser.
     */
    protected ModelParser $modelParser;

    /**
     * Invoke an inaccessible method.
     */
    public function __call(string $name, array $arguments)
    {
        return call_user_func_array([$this->get(), $name], $arguments);
    }

    /**
     * Create the model accessor instance.
     *
     * @param class-string<Model> $model
     */
    public function __construct(
        /**
         * Model cache unique snake-case name.
         */
        public string $name,

        /**
         * Model class name.
         *
         * @var class-string<Model>
         */
        public string $model,
    ) {
        $this->modelCompiler = $this->makeModelCompiler();
        $this->path = $this->makePath();
        $this->modelParser = $this->makeModelParser();
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
     * Ensure model cache data is in storage.
     *
     * If not in memory, also compiles the model cache.
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
     * Clear model cache data from memory and storage.
     */
    public function clear(): void
    {
        $this->unload();
        $this->delete();
    }

    /**
     * Compile model cache data to memory.
     */
    public function compile(): void
    {
        $this->modelCache = $this->modelCompiler->compile();
    }

    /**
     * Delete model cache data from storage.
     */
    public function delete(): void
    {
        if ($this->getDisk()->exists($this->path)) {
            $this->getDisk()->delete($this->path);
        }
    }

    /**
     * Get the model cache.
     *
     * If not in memory, loads the model cache file.
     *
     * If not in storage, compiles the model cache.
     */
    public function get(): ModelCache
    {
        if (!$this->isLoaded()) {
            $this->cache();
            $this->load();
        }
        return $this->modelCache;
    }

    /**
     * Get the model cache data file size in storage.
     */
    public function getSize(): ?int
    {
        return $this->isCached() ? $this->getDisk()->size($this->path) : null;
    }

    /**
     * Get the model cache file's last modified timestamp in storage.
     */
    public function getTimestamp(): ?int
    {
        return $this->isCached() ? $this->getDisk()->lastModified($this->path) : null;
    }

    /**
     * Check whether model cache data is in storage.
     */
    public function isCached(): bool
    {
        return $this->getDisk()->exists($this->path);
    }

    /**
     * Check whether model cache data is loaded to memory.
     */
    public function isLoaded(): bool
    {
        return isset($this->modelCache);
    }

    /**
     * Load model cache data from storage.
     */
    public function load(): void
    {
        if (!$this->isCached()) {
            throw new RuntimeException('Model is not cached.');
        }
        $this->modelCache = $this->parse($this->getDisk()->get($this->path));
    }

    /**
     * Save model cache data from memory to storage.
     */
    public function save(): void
    {
        if (!$this->isLoaded()) {
            throw new RuntimeException('Model cache is not set.');
        }
        $contents = $this->unparse($this->modelCache);
        $this->getDisk()->put($this->path, $contents);
    }

    /**
     * Clear model cache data from memory.
     */
    public function unload(): void
    {
        unset($this->modelCache);
    }

    /**
     * Get the lazy-loaded filesystem instance.
     */
    protected function getDisk(): Filesystem
    {
        $this->disk ??= $this->makeDisk();
        return $this->disk;
    }

    /**
     * Create a filesystem instance for the current context.
     */
    protected function makeDisk(): Filesystem
    {
        return Storage::build(config('roa.cache'));
    }

    /**
     * Create a model compiler instance for the current context.
     */
    protected function makeModelCompiler(): ModelCompiler
    {
        return new ModelCompiler($this->model);
    }

    /**
     * Create a cache file path for the current context.
     */
    protected function makePath(): string
    {
        return "{$this->name}.cache";
    }

    /**
     * Create a model parser instance for the current context.
     */
    protected function makeModelParser(): ModelParser
    {
        return new ModelParser();
    }

    /**
     * Parse serialized data into a `ModelCache` instance.
     */
    protected function parse(string $data): ModelCache
    {
        return $this->modelParser->parse($data);
    }

    /**
     * Turn a `ModelCache` instance into serialized data.
     */
    protected function unparse(ModelCache $data): string
    {
        return $this->modelParser->unparse($data);
    }
}
