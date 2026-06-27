<?php

use Workbench\App\Attributes\Ruleset;
use Covaleski\Laravel\Catalog\Facades\Catalog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Catalog::each(function ($model) {
    $uri = '/api/' . str($model->model)->classBasename()->kebab()->plural();
    Route::get($uri, fn () => response()->json($model->model::all()));
    Route::post($uri, function (Request $request) use ($model) {
        $attributes = $model->getAttributes(Ruleset::class);
        $rules = collect($attributes)->pluck('rules', 'attribute')->all();
        $values = $request->validate($rules);
        $model = new $model->model;
        $model->fill($values)->save();
        return response()->json($model, 201);
    });
});
