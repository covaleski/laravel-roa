<?php

use App\Attributes\Ruleset;
use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/csrf', function () {
    return response(csrf_token(), 200, [
        'Content-Type' => 'text/plain',
    ]);
});

Resource::each(function ($resource) {
    Route::get(
        "/api/{$resource->name}",
        fn () => response()->json($resource->model::all()),
    );
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
