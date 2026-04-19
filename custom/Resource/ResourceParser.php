<?php

namespace Covaleski\LaravelRoa\Resource;

use Covaleski\LaravelRoa\Interfaces\ResourceParserInterface;

class ResourceParser
{
    /**
     * Parse serialized data into a `ResourceCache` instance.
     */
    public function parse(string $data): ResourceCache
    {
        return unserialize($data);
    }

    /**
     * Turn a `ResourceCache` instance into serialized data.
     */
    public function unparse(ResourceCache $data): string
    {
        return serialize($data);
    }
}
