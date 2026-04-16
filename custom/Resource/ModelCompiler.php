<?php

namespace Covaleski\LaravelRoa\Resource;

use Covaleski\LaravelRoa\Attributes\ResourceName;
use Illuminate\Support\Str;
use ReflectionClass;

class ModelCompiler
{
    /**
     * Reflection class instance.
     */
    final protected ReflectionClass $reflection;

    /**
     * Compile the specified model class to a resource instance.
     */
    public function compile(string $class_name): Resource
    {
        return $this->compiling($class_name, function () use ($class_name) {
            $resource = new Resource();
            $resource->name = $this->compileName($class_name);
            $resource->model = $class_name;
            return $resource;
        });
    }

    /**
     * Get the resource name of a model class.
     */
    public function compileName(string $class_name): string
    {
        return $this->compiling($class_name, function () use ($class_name) {
            $attributes = $this->reflection->getAttributes(ResourceName::class);
            return isset($attributes[0])
                ? $attributes[0]->newInstance()->name
                : Str::plural(Str::kebab(class_basename($class_name)));
        });
    }

    /**
     * Run the specified compilation callback for the specified class.
     *
     * @template TResult
     * @param class-string<Model> $class_name
     * @param callable(): TResult $callback
     * @return TResult
     */
    protected function compiling(string $class_name, callable $callback): mixed
    {
        if (isset($this->reflection)) {
            return call_user_func($callback);
        } else {
            try {
                $this->reflection = new ReflectionClass($class_name);
                return call_user_func($callback);
            } finally {
                unset($this->reflection);
            }
        }
    }
}
