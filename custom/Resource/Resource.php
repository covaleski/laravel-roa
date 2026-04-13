<?php

namespace Covaleski\LaravelRoa\Resource;

class Resource
{
    /**
     * Model that originated the resource.
     */
    public string $model;

    /**
     * Resource unique snake-case name.
     */
    public string $name;
}
