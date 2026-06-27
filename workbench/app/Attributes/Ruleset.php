<?php

namespace Workbench\App\Attributes;

use Attribute;
use Covaleski\Laravel\Catalog\Interfaces\ResourceAttributeInterface;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class Ruleset implements ResourceAttributeInterface
{
    /**
     * Create the attribute instance.
     */
    public function __construct(
        /**
         * Model attribute name.
         */
        public string $attribute,

        /**
         * Validation rules.
         */
        public string|array $rules,
    ) {
        //
    }
}
