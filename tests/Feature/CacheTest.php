<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Covaleski\Laravel\Catalog\Facades\Resource;
use Tests\TestCase;

class CacheTest extends TestCase
{
    /**
     * Ensure the map is compiled and cached when queried.
     */
    public function test_loads_on_demand(): void
    {
        Resource::wipe();
        $this->assertFalse(Resource::isCached());
        $this->assertFalse(Resource::isLoaded());
        Resource::each(fn ($r) => $this->assertFalse($r->isCached()));
        Resource::each(fn ($r) => $this->assertFalse($r->isLoaded()));
        Resource::exists('users');
        $this->assertTrue(Resource::isCached());
        $this->assertTrue(Resource::isLoaded());
        foreach (Resource::all() as $resource) {
            $this->assertFalse($resource->isLoaded());
            $this->assertFalse($resource->isCached());
            $resource->name;
            $this->assertFalse($resource->isLoaded());
            $this->assertFalse($resource->isCached());
            $resource->relationships;
            $this->assertTrue($resource->isLoaded());
            $this->assertTrue($resource->isCached());
        }
    }
}
