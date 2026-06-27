<?php

namespace Covaleski\Laravel\Catalog\Providers;

use Covaleski\Laravel\Catalog\Console\Commands;
use Covaleski\Laravel\Catalog\Model\ModelMap;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\ServiceProvider;

class PackageServiceProvider extends ServiceProvider
{
    /**
     * Package root path.
     */
    protected string $path;

    /**
     * Create the service provider instance.
     *
     * Adds the package vendor directory path for convenience.
     */
    public function __construct(Application $app)
    {
        $this->path = dirname(dirname(__DIR__));
        return parent::__construct($app);
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            "{$this->path}/config/roa.php",
            'roa',
        );
        $this->app->singleton(ModelMap::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->publishes(
            [
                "{$this->path}/config/roa.php" => config_path('roa.php'),
            ],
        );
        Storage::macro('root', function () {
            return Storage::build([
                'driver' => 'local',
                'root' => base_path(),
            ]);
        });
        if ($this->app->runningInConsole()) {
            $this->bootConsole();
        }
    }

    /**
     * Bootstrap console services.
     */
    protected function bootConsole(): void
    {
        $this->commands([
            Commands\CatalogCacheCommand::class,
            Commands\CatalogClearCommand::class,
            Commands\CatalogListCommand::class,
            Commands\CatalogShowCommand::class,
        ]);
        $this->optimizes(
            optimize: 'catalog:cache',
            clear: 'catalog:clear',
            key: 'covaleski/catalog',
        );
    }
}
