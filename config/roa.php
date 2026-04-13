<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | This option defines how the resource cache files are compiled and stored.
    |
    */

    'cache' => [
        'driver' => 'local',
        'root' => storage_path('resources'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Directories
    |--------------------------------------------------------------------------
    |
    | This option defines which directories should be mapped to find and
    | compile models into resource cache files.
    |
    */

    'directories' => [
        'app/Models',
    ],

];
