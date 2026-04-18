# Laravel Resource-Oriented Architecture

This package provides utilities for building Laravel applications based on a
resource-oriented approach. It caches information from your Eloquent models for
fast access during routing and other operations.

## Installation

Install this package using the Composer package manager:

```sh
composer require covaleski/laravel-roa
```

## Usage

### Accessing Mapped Resources

By default, all models from `app/Models` are mapped and compiled to resource
cache files that can be loaded as a `Covaleski\LaravelRoa\Resource\Resource`
objects on demand using the `Covaleski\LaravelRoa\Facades\Resource` facade.

The following example creates a simple JSON GET route for each model:

```php
use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Support\Facades\Route;

Resource::each(function ($resource) {
    Route::get(
        "/api/{$resource->name}",
        fn () => response()->json($resource->model::all()),
    );
});
```

## Configuration

By default, all models in `app/Models` are mapped as resources and all cache
files are saved in the `storage/resources` directory.

Use the `roa.php` configuration file to change mapped directories and output
destinations.

```sh
php artisan vendor:publish --tag=laravel-roa-config
```

## Commands

| Command | Description |
| --- | --- |
| `resource:list` | Compile and list all mapped resources. |
| `resource:clear` | Delete all currently compiled resource cache files. |
| `resource:cache` | Clear cache files and re-compile all mapped resources. |
