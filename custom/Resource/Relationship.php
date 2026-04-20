<?php

namespace Covaleski\LaravelRoa\Resource;

class Relationship
{
    /**
     * Create the relationship instance.
     */
    public function __construct(
        /**
         * Relation type class name.
         */
        public string $relation,

        /**
         * Related model class name.
         */
        public string $model,

        /**
         * Related resource name.
         */
        public string $resource,
    ) {
        //
    }
}
