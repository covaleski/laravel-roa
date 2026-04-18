<?php

namespace Covaleski\LaravelRoa\Resource;

use Covaleski\LaravelRoa\Interfaces\ResourceAttributeInterface;
use Illuminate\Database\Eloquent\Model;

class ResourceCache
{
    /**
     * Attributes.
     *
     * @var array<int, ResourceAttributeInterface>
     */
    public array $attributes;

    /**
     * Model that originated the resource.
     *
     * @var class-string<Model>
     */
    public string $model;

    /**
     * Resource unique snake-case name.
     */
    public string $name;

    /**
     * Get the first attribute of the specified class name.
     *
     * @template TAttribute of ResourceAttributeInterface
     * @param class-string<TAttribute> $type
     * @return ?TAttribute
     */
    public function getAttribute(string $type): ?ResourceAttributeInterface
    {
        foreach ($this->attributes as $attribute) {
            if (is_a($attribute, $type)) {
                return $attribute;
            }
        }
        return null;
    }

    /**
     * Get all attributes of the specified class name.
     *
     * @template TAttribute of ResourceAttributeInterface
     * @param class-string<TAttribute> $type
     * @return array<int, TAttribute>
     */
    public function getAttributes(string $type): array
    {
        return array_filter($this->attributes, fn ($v) => is_a($v, $type));
    }
}
