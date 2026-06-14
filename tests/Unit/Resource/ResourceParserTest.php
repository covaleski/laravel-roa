<?php

namespace Tests\Unit\Resource;

use Covaleski\LaravelRoa\Resource\ResourceCache;
use Covaleski\LaravelRoa\Resource\ResourceParser;
use PHPUnit\Framework\TestCase;

class ResourceParserTest extends TestCase
{
    /**
     * Ensure the map is compiled and cached when queried.
     */
    public function test_parses_and_unparses(): void
    {
        $parser = new ResourceParser();
        $resource = new ResourceCache();
        $resource->model = 'Foo\\Bar';
        $resource->name = 'bars';
        $resource->attributes = [];
        $resource->relationships = [];
        $parsed = $parser->unparse($resource);
        $unparsed = $parser->parse($parsed);
        $this->assertEquals($resource, $unparsed);
    }
}
