<?php

namespace Covaleski\LaravelRoa\Providers;

use Covaleski\LaravelRoa\Resource\ModelCompiler;
use Covaleski\LaravelRoa\Resource\ResourceLoader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(ModelCompiler::class);
        $this->app->singleton(ResourceLoader::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::macro('root', function () {
            return Storage::build([
                'driver' => 'local',
                'root' => base_path(),
            ]);
        });
    }
}
