<?php

namespace Tests\Feature;

// use Illuminate\Foundation\Testing\RefreshDatabase;

use Covaleski\Laravel\Catalog\Facades\Catalog;
use Tests\TestCase;

class CacheTest extends TestCase
{
    /**
     * Ensure the map is compiled and cached when queried.
     */
    public function test_loads_on_demand(): void
    {
        Catalog::wipe();
        $this->assertFalse(Catalog::isCached());
        $this->assertFalse(Catalog::isLoaded());
        Catalog::each(fn ($r) => $this->assertFalse($r->isCached()));
        Catalog::each(fn ($r) => $this->assertFalse($r->isLoaded()));
        Catalog::exists('users');
        $this->assertTrue(Catalog::isCached());
        $this->assertTrue(Catalog::isLoaded());
        foreach (Catalog::all() as $model) {
            $this->assertFalse($model->isLoaded());
            $this->assertFalse($model->isCached());
            $model->model;
            $this->assertFalse($model->isLoaded());
            $this->assertFalse($model->isCached());
            $model->relationships;
            $this->assertTrue($model->isLoaded());
            $this->assertTrue($model->isCached());
        }
    }
}
