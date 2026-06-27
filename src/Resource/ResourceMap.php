<?php

namespace Covaleski\Laravel\Catalog\Resource;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use InvalidArgumentException;
use RuntimeException;

use function Covaleski\Laravel\Catalog\file_get_classes;
use function Illuminate\Filesystem\join_paths;

class ResourceMap
{
    /**
     * Cache directory disk instance.
     */
    protected Filesystem $cacheDisk;

    /**
     * Map data.
     *
     * Links resource names to model class names.
     *
     * @var array<string, class-string<Model>>
     */
    final protected array $map;

    /**
     * Map file path.
     */
    protected string $path;

    /**
     * Instantiated resource accessors.
     *
     * @var array<string, ResourceAccessor>
     */
    protected array $resourceAccessors = [];

    /**
     * Create the resource loader instance.
     */
    public function __construct()
    {
        $this->path = $this->makePath();
    }

    /**
     * Get all mapped resources.
     *
     * @return array<string, ResourceAccessor>
     */
    public function all(): array
    {
        $this->ensureMap();
        return collect($this->map)
            ->keys()
            ->map($this->get(...))
            ->toArray();
    }

    /**
     * Ensure map data is in storage.
     *
     * If not in memory, also compiles the map.
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
     * Clear map data from memory and storage.
     */
    public function clear(): void
    {
        $this->unload();
        $this->delete();
    }

    /**
     * Compile map data to memory.
     */
    public function compile(): void
    {
        $this->map = $this->mapAll();
    }

    /**
     * Delete map data from storage.
     */
    public function delete(): void
    {
        if ($this->getCacheDisk()->exists($this->path)) {
            $this->getCacheDisk()->delete($this->path);
        }
    }

    /**
     * Execute a callback over each mapped resource.
     *
     * @param callable(ResourceAccessor $resource, string $name): void $callback
     */
    public function each(callable $callback): void
    {
        foreach ($this->all() as $name => $resource) {
            if ($callback($resource, $name) === false) {
                break;
            }
        }
    }

    /**
     * Check whether a resource is mapped.
     */
    public function exists(string $name): bool
    {
        $this->ensureMap();
        return isset($this->map[$name]);
    }

    /**
     * Get a resource by its name.
     */
    public function get(string $name): ResourceAccessor
    {
        if (!$this->exists($name)) {
            $message = "No resource is mapped as '{$name}'";
            throw new InvalidArgumentException($message);
        }
        $this->resourceAccessors[$name] ??= new ResourceAccessor(
            $name,
            $this->map[$name],
        );
        return $this->resourceAccessors[$name];
    }

    /**
     * Get mapped directories.
     *
     * @return Collection<int, string>
     */
    public function getDirectories(): Collection
    {
        return collect(config('roa.directories', []));
    }

    /**
     * Get the map cache file's filename.
     */
    public function getFilename(): string
    {
        return $this->getCacheDisk()->path($this->path);
    }

    /**
     * Get the map cache file's size.
     */
    public function getSize(): int
    {
        return $this->getCacheDisk()->size($this->path);
    }

    /**
     * Get the map cache file's last modification time.
     */
    public function getTimestamp(): int
    {
        return $this->getCacheDisk()->lastModified($this->path);
    }

    /**
     * Check whether map data is in storage.
     */
    public function isCached(): bool
    {
        return $this->getCacheDisk()->exists($this->path);
    }

    /**
     * Check whether map data is loaded to memory.
     */
    public function isLoaded(): bool
    {
        return isset($this->map);
    }

    /**
     * Load map data from storage.
     */
    public function load(): void
    {
        if (!$this->isCached()) {
            throw new RuntimeException('Resource is not cached.');
        }
        $this->map = $this->parse($this->getCacheDisk()->get($this->path));
    }

    /**
     * Save map data from memory to storage.
     */
    public function save(): void
    {
        if (!$this->isLoaded()) {
            throw new RuntimeException('Resource cache is not set.');
        }
        $this->getCacheDisk()->put($this->path, $this->unparse($this->map));
    }

    /**
     * Clear map data from memory.
     */
    public function unload(): void
    {
        unset($this->map, $this->resourceAccessors);
    }

    /**
     * Clear map and resource cache data from memory and storage.
     */
    public function wipe(): void
    {
        foreach ($this->getCacheDisk()->files() as $file) {
            if (Str::endsWith($file, '.cache')) {
                $this->getCacheDisk()->delete($file);
            }
        }
        $this->clear();
    }

    /**
     * Compile the resource name of a model class.
     */
    protected function compileName(string $class_name): string
    {
        return (new ModelCompiler($class_name))->compileName();
    }

    /**
     * Get the lazy-loaded filesystem instance for the cache directory.
     */
    protected function getCacheDisk(): Filesystem
    {
        $this->cacheDisk ??= $this->makeCacheDisk();
        return $this->cacheDisk;
    }

    /**
     * Ensure map data is loaded to memory.
     */
    protected function ensureMap(): void
    {
        if (!$this->isLoaded()) {
            $this->cache();
            $this->load();
        }
    }

    /**
     * Create a filesystem instance for the cache directory.
     */
    protected function makeCacheDisk(): Filesystem
    {
        return Storage::build(config('roa.cache'));
    }

    /**
     * Create a resource map file path for the current context.
     */
    protected function makePath(): string
    {
        return "resources.map";
    }

    /**
     * Create a map for all application models.
     *
     * @return array<string, class-string<Model>>
     */
    protected function mapAll(): array
    {
        return $this->getDirectories()
            ->flatMap($this->mapDirectory(...))
            ->toArray();
    }

    /**
     * Create a map for models in the specified PHP code.
     *
     * @return array<string, class-string<Model>>
     */
    protected function mapCode(string $code): array
    {
        return collect(file_get_classes($code))
            ->filter(fn ($class_name) => is_a($class_name, Model::class, true))
            ->keyBy(fn ($class_name) => $this->compileName($class_name))
            ->toArray();
    }

    /**
     * Create a map for models in the specified directory.
     *
     * @return array<string, class-string<Model>>
     */
    protected function mapDirectory(string $directory): array
    {
        return collect(scandir($directory))
            ->diff(['.', '..'])
            ->map(fn ($basename) => join_paths($directory, $basename))
            ->filter(fn ($filename) => is_file($filename))
            ->flatMap($this->mapFile(...))
            ->toArray();
    }

    /**
     * Create a map for models in the specified PHP file.
     *
     * @return array<string, class-string<Model>>
     */
    protected function mapFile(string $filename): array
    {
        return $this->mapCode(file_get_contents($filename));
    }

    /**
     * Parse serialized data into a map.
     *
     * @return array<string, class-string<Model>>
     */
    protected function parse(string $data): array
    {
        return json_decode($data, true);
    }

    /**
     * Turn a map into serialized data.
     *
     * @param array<string, class-string<Model>> $data
     */
    protected function unparse(array $data): string
    {
        return json_encode($data);
    }
}
