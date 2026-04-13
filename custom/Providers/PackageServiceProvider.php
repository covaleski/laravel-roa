<?php

namespace Covaleski\LaravelRoa\Providers;

use Covaleski\LaravelRoa\Resource\ModelCompiler;
use Covaleski\LaravelRoa\Resource\ResourceLoader;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Project root directory.
     */
    protected string $root;

    /**
     * Create the service provider instance.
     */
    public function __construct($app)
    {
        $this->root = dirname(dirname(__DIR__));
        return parent::__construct($app);
    }

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
        $this->publishes(
            paths: [
                "{$this->root}/app/config/roa.php" => config_path('roa.php'),
            ],
            groups: 'laravel-roa-config',
        );
        Storage::macro('root', function () {
            return Storage::build([
                'driver' => 'local',
                'root' => base_path(),
            ]);
        });
    }
}
