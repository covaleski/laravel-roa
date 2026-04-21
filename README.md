# Laravel Resource-Oriented Architecture Utility

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
cache files that can be loaded as a resource objects on demand using the
`Resource` facade.

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

### Resource Attributes

All model attributes that implement the `ResourceAttributeInterface` are
automatically added to the resource object, serving as additional metadata
when accessing cached resources.

The following example uses attributes to store model validation rules directly
into the model's metadata:

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

### Lazy Loading

Resource cache data is lazy-loaded, meaning basic information such as the
resource name, model class and cache status are available without the need
to read or parse any cached data. When more complex data - like attributes
and relationships - is accessed, the cache file is then read:

```php
use Covaleski\LaravelRoa\Facades\Resource;

// No cache data will be loaded for the following code.
$resource = Resource::get('flights');
echo "The '{$resource->name}' resource points to the {$resource->model} model.";
echo PHP_EOL;

// The 'flights' resource cache data will be loaded by the following code.
$relationships = count($resource->relationships);
echo "The '{$resource->name}' has {$relationships} relationships.";
echo PHP_EOL;
```

### Dynamic Compilation

By default, models are compiled to resource cache files on demand, meaning the
first loading operation of a model that is not yet compiled will be slower than
the subsequent ones.

You can use the `resource:cache` command to compile and cache the resource map
and all mapped models at once.

## Configuration

Publish the `roa.php` configuration file to customize the package behavior:

```sh
php artisan vendor:publish --tag=laravel-roa-config
```

The following directives are available in `config/roa.php`:

| Key | Description |
| --- | --- |
| `cache` | Where to store resource cache files. |
| `directories` | Where to find models. |

## Commands

The following commands are available for resource maintenance:

| Command | Description |
| --- | --- |
| `resource:cache` | Update mapped models and cache all resources. |
| `resource:clear` | Clear mapped models and currently cached resources. |
| `resource:list` | List all currently mapped models. |
| `resource:show {resource}` | Show details of the specified resource. |
