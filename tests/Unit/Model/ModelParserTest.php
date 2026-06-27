<?php

namespace Tests\Unit\Model;

use Covaleski\Laravel\Catalog\Model\ModelCache;
use Covaleski\Laravel\Catalog\Model\ModelParser;
use PHPUnit\Framework\TestCase;

class ModelParserTest extends TestCase
{
    /**
     * Ensure the map is compiled and cached when queried.
     */
    public function test_parses_and_unparses(): void
    {
        $parser = new ModelParser();
        $model = new ModelCache();
        $model->model = 'Foo\\Bar';
        $model->attributes = [];
        $model->relationships = [];
        $parsed = $parser->unparse($model);
        $unparsed = $parser->parse($parsed);
        $this->assertEquals($model, $unparsed);
    }
}
