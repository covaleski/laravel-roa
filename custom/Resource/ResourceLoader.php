<?php

namespace Covaleski\LaravelRoa\Resource;

use Covaleski\LaravelRoa\Attributes\IsResource;
use Covaleski\LaravelRoa\Support\CachedArray;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use ReflectionClass;

use function Covaleski\LaravelRoa\file_get_classes;
use function Illuminate\Filesystem\join_paths;

class ResourceLoader
{
    /**
     * Cache directory disk instance.
     */
    protected Filesystem $cacheDisk;

    /**
     * Currently loaded resource instances.
     *
     * @var array<string, Resource>
     */
    protected array $instances = [];

    /**
     * Mapped resources.
     */
    protected CachedArray $resources;

    /**
     * Project root directory disk instance.
     */
    protected Filesystem $rootDisk;

    /**
     * Create the resource loader instance.
     */
    public function __construct(
        /**
         * Model compiler instance.
         */
        protected ModelCompiler $modelCompiler,
    ) {
        $this->cacheDisk = Storage::build(config('roa.cache'));
        $this->resources = new CachedArray(
            $this->cacheDisk,
            'resources.map',
            $this->map(...),
        );
        $this->rootDisk = Storage::root();
    }

    /**
     * Get all mapped resources.
     *
     * @return array<string, Resource>
     */
    public function all(): array
    {
        return collect($this->resources)
            ->keys()
            ->map($this->get(...))
            ->toArray();
    }

    /**
     * Check whether a resource is mapped.
     */
    public function exists(string $name): bool
    {
        return isset($this->resources[$name]);
    }

    /**
     * Get a resource by its unique name.
     */
    public function get(string $name): Resource
    {
        $this->instances[$name] ??= $this->load($name);
        return $this->instances[$name];
    }

    /**
     * Load a resource by its unique name.
     */
    public function load(string $name): Resource
    {
        if (!$this->isCached($name)) {
            $this->cache($name);
        }
        return $this->unserialize($this->read($name));
    }

    /**
     * Compile and cache a resource.
     */
    protected function cache(string $name): void
    {
        $this->save($this->compile($name));
    }

    /**
     * Compile a resource.
     */
    protected function compile(string $name): Resource
    {
        if (!$this->exists($name)) {
            $message = "No resource is mapped as '{$name}'";
            throw new InvalidArgumentException($message);
        }
        return $this->modelCompiler->compile($this->resources[$name]);
    }

    /**
     * Check whether the specified resource has a cache file.
     */
    protected function isCached(string $name): bool
    {
        return $this->cacheDisk->exists("{$name}.cache");
    }

    /**
     * Get an updated resource map for the application.
     *
     * @return array<string, class-string<Model>
     */
    protected function map(): array
    {
        return collect(config('roa.directories'))
            ->flatMap($this->mapDirectory(...))
            ->toArray();
    }

    /**
     * Map resources in the specified directory.
     *
     * @return array<string, class-string<Model>
     */
    protected function mapDirectory(string $path): array
    {
        return collect($this->rootDisk->files($path))
            ->flatMap(fn ($file) => $this->parseFile($file))
            ->toArray();
    }

    /**
     * Parse PHP code into a resource name and model class name.
     *
     * @return array<string, class-string<Model>>
     */
    protected function parseCode(string $code): array
    {
        return collect(file_get_classes($code))
            ->filter(function (string $class_name) {
                return is_a($class_name, Model::class, true);
            })
            ->mapWithKeys(function (string $class_name) {
                return [$this->modelCompiler->compileName($class_name) => $class_name];
            })
            ->toArray();
    }

    /**
     * Parse a file into a resource name and model class name.
     *
     * @return array<string, class-string<Model>>
     */
    protected function parseFile(string $path): array
    {
        return $this->parseCode($this->rootDisk->get($path));
    }

    /**
     * Get the cached contents of the specified resource.
     */
    protected function read(string $name): string
    {
        return $this->cacheDisk->get("{$name}.cache");
    }

    /**
     * Save the specified resource to its cache file.
     */
    protected function save(Resource $resource): void
    {
        $data = $this->serialize($resource);
        $this->cacheDisk->put("{$resource->name}.cache", $data);
    }

    /**
     * Serialize a resource into cache data.
     */
    protected function serialize(Resource $resource): string
    {
        return serialize($resource);
    }

    /**
     * Unserialize the contents of a file into a resource instance.
     */
    protected function unserialize(string $data): Resource
    {
        return unserialize($data);
    }
}
