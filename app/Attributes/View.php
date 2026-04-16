<?php

namespace App\Attributes;

use Attribute;
use Covaleski\LaravelRoa\Interfaces\ResourceAttributeInterface;
use Illuminate\Support\Facades\Route;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class View implements ResourceAttributeInterface
{
    /**
     * Create the attribute instance.
     */
    public function __construct(
        /**
         * URI path.
         */
        public string $uri,

        /**
         * View name.
         */
        public string $view,

        /**
         * View data.
         */
        public array $data = [],
    ) {
        //
    }

    /**
     * Add a route based on this attribute's properties.
     */
    public function route(): void
    {
        Route::view($this->uri, $this->view, $this->data);
    }
}
