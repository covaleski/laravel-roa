<?php

namespace Covaleski\Laravel\Catalog\Model;

use Covaleski\Laravel\Catalog\Interfaces\ModelParserInterface;

class ModelParser
{
    /**
     * Parse serialized data into a `ModelCache` instance.
     */
    public function parse(string $data): ModelCache
    {
        return unserialize($data);
    }

    /**
     * Turn a `ModelCache` instance into serialized data.
     */
    public function unparse(ModelCache $data): string
    {
        return serialize($data);
    }
}
