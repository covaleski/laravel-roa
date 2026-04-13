<?php

namespace Covaleski\LaravelRoa\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ResourceName
{
    /**
     * Create the attribute instance.
     */
    public function __construct(
        /**
         * Custom resource name.
         */
        public ?string $name = null,
    ) {
        //
    }
}
