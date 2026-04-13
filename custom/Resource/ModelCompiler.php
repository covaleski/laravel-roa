<?php

namespace Covaleski\LaravelRoa\Resource;

use Covaleski\LaravelRoa\Attributes\ResourceName;
use Illuminate\Support\Str;
use ReflectionClass;

class ModelCompiler
{
    /**
     * Compile the specified model class to a resource instance.
     */
    public function compile(string $class_name): Resource
    {
        $resource = new Resource();
        $resource->name = $this->generateName($class_name);
        $resource->model = $class_name;
        return $resource;
    }

    /**
     * Get the resource name of a model class.
     */
    public function compileName(string $class_name): string
    {
        $reflection = new ReflectionClass($class_name);
        $attributes = $reflection->getAttributes(ResourceName::class);
        if (isset($attributes[0])) {
            return $attributes[0]->newInstance()->name;
        } else {
            return $this->generateName($class_name);
        }
    }

    /**
     * Generate a resource name for a model class.
     */
    protected function generateName(string $class_name): string
    {
        return Str::plural(Str::kebab(class_basename($class_name)));
    }
}
