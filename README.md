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

Use the `Resource` facade to access information on mapped resources:

```php
use Covaleski\LaravelRoa\Facades\Resource;

Resource::all(); // Get an array with all compiled resources
Resource::get('users'); // Get a specific compiled resource
Resource::exists('roles'); // Check if a resource is mapped
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
