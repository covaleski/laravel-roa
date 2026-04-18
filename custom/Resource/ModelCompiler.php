<?php

namespace Covaleski\LaravelRoa\Resource;

use Covaleski\LaravelRoa\Attributes\ResourceName;
use Covaleski\LaravelRoa\Interfaces\ResourceAttributeInterface;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use ReflectionAttribute;
use ReflectionClass;

class ModelCompiler
{
    /**
     * Reflection class instance.
     */
    final protected ReflectionClass $reflection;

    /**
     * Create the model compiler instance.
     *
     * @param class-string<Model> $model
     */
    public function __construct(
        /**
         * Model class name.
         *
         * @var class-string<Model>
         */
        protected string $model,
    ) {
        //
    }

    /**
     * Compile the specified model class to a resource instance.
     */
    public function compile(): ResourceCache
    {
        $this->initialize();
        $resource = new ResourceCache();
        $resource->name = $this->compileName();
        $resource->model = $this->model;
        $resource->attributes = $this->compileAttributes();
        return $resource;
    }

    /**
     * Get the resource attributes of a model class.
     *
     * @return array<int, ResourceAttributeInterface>
     */
    public function compileAttributes(): array
    {
        $this->initialize();
        return Arr::map(
            $this->reflection->getAttributes(
                ResourceAttributeInterface::class,
                ReflectionAttribute::IS_INSTANCEOF,
            ),
            fn ($v) => $v->newInstance(),
        );
    }

    /**
     * Get the resource name of a model class.
     */
    public function compileName(): string
    {
        $this->initialize();
        $attributes = $this->reflection->getAttributes(ResourceName::class);
        return isset($attributes[0])
            ? $attributes[0]->newInstance()->name
            : Str::plural(Str::kebab(class_basename($this->model)));
    }

    /**
     * Initialize lazy loaded properties.
     */
    public function initialize(): void
    {
        $this->reflection ??= new ReflectionClass($this->model);
    }
}
