<?php

namespace Covaleski\LaravelRoa\Resource;

use Covaleski\LaravelRoa\Attributes\ResourceName;
use Covaleski\LaravelRoa\Interfaces\ResourceAttributeInterface;
use Covaleski\LaravelRoa\Traits\ParsesDocComments;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use phpDocumentor\Reflection\DocBlock\Tags\Return_;
use phpDocumentor\Reflection\DocBlockFactory;
use phpDocumentor\Reflection\Types\Object_;
use ReflectionAttribute;
use ReflectionClass;
use ReflectionMethod;
use ReflectionNamedType;

class ModelCompiler
{
    use ParsesDocComments;

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
     * Compile the model to a resource cache instance.
     */
    public function compile(): ResourceCache
    {
        $this->initialize();
        $resource = new ResourceCache();
        $resource->name = $this->compileName();
        $resource->model = $this->model;
        $resource->attributes = $this->compileAttributes();
        $resource->relationships = $this->compileRelationships();
        return $resource;
    }

    /**
     * Get the resource attributes from the model.
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
     * Get the resource name from the model.
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
     * Get the specified relationship from the model.
     */
    protected function compileRelationship(string $key): Relationship
    {
        $relation = $this->getRelation($key);
        $related = $relation->getRelated();
        return new Relationship(
            relation: $relation::class,
            model: $related::class,
            resource: (new ModelCompiler($related::class))->compileName(),
        );
    }

    /**
     * Get all relationships from the model.
     *
     * @return array<string, Relationship>
     */
    public function compileRelationships(): array
    {
        $this->initialize();
        return collect($this->reflection->getMethods())
            ->filter(fn ($method) => $this->isRelation($method))
            ->keyBy(fn ($method) => $method->getName())
            ->map(fn ($method) => $this->compileRelationship($method->getName()))
            ->all();
    }

    /**
     * Initialize lazy loaded properties.
     */
    public function initialize(): void
    {
        $this->reflection ??= new ReflectionClass($this->model);
        $this->context = $this->makeContext($this->model, $this->reflection->getFileName());
        $this->docBlockFactory = DocBlockFactory::createInstance();
    }

    /**
     * Get a relation instance from the model.
     */
    protected function getRelation(string $key): Relation
    {
        return call_user_func([new $this->model, $key]);
    }

    /**
     * Check whether a method is a relationship.
     */
    protected function isRelation(ReflectionMethod $method): bool
    {
        return $method->isPublic()
            && $method->getNumberOfParameters() === 0
            && $this->returnsRelation($method);
    }

    /**
     * Check whether a method returns a relationship instance.
     */
    protected function returnsRelation(ReflectionMethod $method): bool
    {
        $doc_block = $this->parseDocComment($method->getDocComment());
        /** @var ?Return_ */
        $return_tag = $doc_block->getTagsByName('return')[0] ?? null;
        $type = $return_tag?->getType() ?? $method->getReturnType();
        if ($type instanceof Object_) {
            $class = (string) $type->getFqsen();
        } elseif ($type instanceof ReflectionNamedType) {
            $class = (string) $type;
        } else {
            $class = null;
        }
        return $class
            && class_exists($class)
            && is_a($class, Relation::class, true);
    }
}
