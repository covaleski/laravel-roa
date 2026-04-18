<?php

use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Resource::each(function ($resource) {
    Route::get(
        "/api/{$resource->name}",
        fn () => response()->json($resource->model::all()),
    );
});
