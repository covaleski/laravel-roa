<?php

namespace Covaleski\Laravel\Catalog\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class CacheName
{
    /**
     * Create the attribute instance.
     */
    public function __construct(
        /**
         * Custom model cache name.
         */
        public ?string $name = null,
    ) {
        //
    }
}
