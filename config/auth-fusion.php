<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Default Driver
    |--------------------------------------------------------------------------
    |
    | This option controls the default authentication driver that will be used
    | by AuthFusion. You may set this to any of the drivers defined below.
    |
    | Supported: "sanctum", "jwt", "passport"
    |
    */

    'driver' => env('AUTH_FUSION_DRIVER', 'sanctum'),

    /*
    |--------------------------------------------------------------------------
    | Driver Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure each driver individually. This allows you to
    | customize the behavior of each driver as needed.
    |
    */

    'drivers' => [
        'sanctum' => [
            'guard' => 'web',
        ],

        'jwt' => [
            'guard' => 'api',
        ],

        'passport' => [
            'guard' => 'web',
        ],
    ],

];

