<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | This option defines where resource cache files will be stored.
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
    | This option defines which directories will be searched to find models
    | that can be compiled into resources.
    |
    */

    'directories' => [
        'app/Models',
    ],

];
