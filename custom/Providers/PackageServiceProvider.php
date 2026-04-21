<?php

namespace Covaleski\LaravelRoa\Providers;

use Covaleski\LaravelRoa\Console\Commands;
use Covaleski\LaravelRoa\Resource\ResourceMap;
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
        $this->app->singleton(ResourceMap::class);
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
        if ($this->app->runningInConsole()) {
            $this->commands([
                Commands\Resource\CacheCommand::class,
                Commands\Resource\ClearCommand::class,
                Commands\Resource\ListCommand::class,
                Commands\Resource\ShowCommand::class,
            ]);
            $this->optimizes(
                optimize: 'resource:cache',
                clear: 'resource:clear',
                key: 'laravel-roa:resources',
            );
        }
        Storage::macro('root', function () {
            return Storage::build([
                'driver' => 'local',
                'root' => base_path(),
            ]);
        });
    }
}
