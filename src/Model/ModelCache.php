<?php

namespace Covaleski\Laravel\Catalog\Model;

use Covaleski\Laravel\Catalog\Interfaces\ModelAttributeInterface;
use Covaleski\Laravel\Catalog\Model\Relationship;
use Illuminate\Database\Eloquent\Model;

class ModelCache
{
    /**
     * Attributes.
     *
     * @var array<int, ModelAttributeInterface>
     */
    public array $attributes;

    /**
     * Model class name.
     *
     * @var class-string<Model>
     */
    public string $model;

    /**
     * Related models.
     *
     * @var array<string, Relationship>
     */
    public array $relationships;

    /**
     * Get the first attribute of the specified class name.
     *
     * @template TAttribute of ModelAttributeInterface
     * @param class-string<TAttribute> $type
     * @return ?TAttribute
     */
    public function getAttribute(string $type): ?ModelAttributeInterface
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
     * @template TAttribute of ModelAttributeInterface
     * @param class-string<TAttribute> $type
     * @return array<int, TAttribute>
     */
    public function getAttributes(string $type): array
    {
        return array_filter($this->attributes, fn ($v) => is_a($v, $type));
    }
}
