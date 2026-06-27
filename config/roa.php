<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cache
    |--------------------------------------------------------------------------
    |
    | This option defines where model cache files will be stored.
    |
    */

    'cache' => [
        'driver' => 'local',
        'root' => storage_path('models'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Directories
    |--------------------------------------------------------------------------
    |
    | This option defines which directories to search for models.
    |
    */

    'directories' => [
        app_path('Models'),
    ],

];
