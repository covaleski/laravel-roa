# Laravel Resource-Oriented Architecture Utility

This package provides utilities for building Laravel applications based on a
[resource-oriented](https://en.wikipedia.org/wiki/Resource-oriented_architecture)
approach.

It maps and caches information from your application's Eloquent models for
quick use with the `Resource` facade.

## Installation

Install this package using the Composer package manager:

```sh
composer require covaleski/laravel-roa
```

## Usage

All the package features can be accessed through the `Resource` facade, which
proxies a `ResourceMap` singleton registered by the package's service provider.

### Accessing Mapped Models

By default, all models in the `app/Models` directory are automatically mapped
and cached. As soon as you install this package, you should be able to access
cached information on them with the `Resource` facade.

The example below adds a simple JSON GET route for each mapped model:

```php
// routes/web.php

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Support\Facades\Route;

Resource::each(function ($resource) {
    Route::get(
        "/api/{$resource->name}",
        fn () => response()->json($resource->model::all()),
    );
});
```

You can list all mapped models along with their cache status using the
`resource:list` command:

```sh
php artisan resource:list
```

### Cached Information

The `ResourceMap` object - proxied by the `Resource` facade - provides access to
`ResourceAccessor` instances along with information on the map itself.

Each `ResourceAccessor` instance provides optimized access to cached data,
loading cache files only [when necessary](#cache-behavior). The following
data is available on each compiled resource through the accessor:

| Property | Stored at | Description |
| --- | --- | --- |
| `name` | Resource map | Resource kebab-case unique name (e.g. "books"). |
| `model` | Resource map | Eloquent model's fully qualified class name. |
| `relationships` | Cache file | Found Eloquent relationships in the model. |
| `attributes` | Cache file | [Custom resource attributes](#custom-attributes). |

> **Note on relationships:**
>
> You must provide minimal type-hint information on Eloquent relationship
> methods or else the compiler won't be able to identify it as so. Both native
> PHP type declarations or PHPDoc `@return` tags are supported.

To see what output the compiler is providing for a specific resource, use the
`resource:show` command:

```sh
php artisan resource:show books
```

### Custom Attributes

Along with default model information, model class attributes that implement the
`ResourceAttributeInterface` are also read and cached, serving as additional
resource metadata.

The example below uses custom attributes to store Laravel validation rules as
model metadata for further use in simple JSON POST routes:

```php
// app/Attributes/Ruleset.php

namespace App\Attributes;

use Attribute;
use Covaleski\LaravelRoa\Interfaces\ResourceAttributeInterface;

#[Attribute(Attribute::TARGET_CLASS|Attribute::IS_REPEATABLE)]
class Ruleset implements ResourceAttributeInterface
{
    public function __construct(
        public string $attribute,
        public string|array $rules,
    ) {
        //
    }
}
```

```php
// app/Models/Book.php

namespace App\Models;

use App\Attributes\Ruleset;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable('isbn', 'title', 'author')]
#[Ruleset('isbn', 'required|unique:books')]
#[Ruleset('title', 'required')]
#[Ruleset('author', 'required')]
class Book extends Model
{
    //
}
```

```php
// routes/web.php

use App\Attributes\Ruleset;
use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Resource::each(function ($resource) {
    Route::post(
        "/api/{$resource->name}",
        function (Request $request) use ($resource) {
            $attributes = $resource->getAttributes(Ruleset::class);
            $rules = collect($attributes)->pluck('rules', 'attribute')->all();
            $values = $request->validate($rules);
            $model = new $resource->model;
            $model->fill($values)->save();
            return response()->json($model, 201);
        },
    );
});
```

## Cache Behavior

By default, cached data is only compiled and loaded when necessary. You can
optimize this behavior using the `resource:cache` command so no compilation
is needed the first time a cache file is required during a request:

```sh
php artisan resource:cache
```

When cache files are outdated, use the `resource:clear` command to wipe cache
data from storage:

```sh
php artisan resource:clear
```

Basic information such as the resource's name, model class and cache status are
available without the need to read or parse any cached data. When more complex
data is accessed (such as relationships or custom attributes), the cache file is
then read:

```php
use Covaleski\LaravelRoa\Facades\Resource;

// Won't require loading the cache file
$resource = Resource::get('flights');
echo "The '{$resource->name}' resource points to the {$resource->model} model.";
echo PHP_EOL;

// Will load the 'flights' resource cache file
$relationships = count($resource->relationships);
echo "The '{$resource->name}' has {$relationships} relationships.";
echo PHP_EOL;
```

Off course, once the cache file is read and loaded into memory, no further file
reading is required for that resource.

## Configuration

Publish the `roa.php` configuration file to customize package behavior:

```sh
php artisan vendor:publish --tag=laravel-roa-config
```

The following directives are available in the `config/roa.php` file:

| Key | Description |
| --- | --- |
| `cache` | Storage configuration for cache files. |
| `directories` | Directories to search for Eloquent model. |

## Motivation

I made this package primarily for myself since models end up being the center
of my Laravel applications logic. It's a way of modularizing generalization
logic I used to copy/paste or rewrite for different purposes and also a way of
sharing code that is useful to me.

The next development steps are documented in the [TODO.md](./TODO.md) file.
