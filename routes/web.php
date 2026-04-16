<?php

use App\Attributes\View;
use Covaleski\LaravelRoa\Facades\Resource;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Resource::each(function ($resource) {
    array_map(
        fn ($v) => Route::view($v->uri, $v->view, $v->data),
        $resource->getAttributes(View::class),
    );
});
