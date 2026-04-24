<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Build Directory
    |--------------------------------------------------------------------------
    |
    | The directory where compiled frontend assets are stored, relative to
    | the backend root. The laravel-vite-plugin will read the manifest from
    | this location.
    |
    */

    'build_directory' => env('VITE_BUILD_DIRECTORY', '../frontend/dist'),

    /*
    |--------------------------------------------------------------------------
    | Hot File Path
    |--------------------------------------------------------------------------
    |
    | The path to the hot file that indicates the Vite dev server is running.
    | Laravel checks this file to determine if it should load assets from the
    | dev server or from the build directory.
    |
    */

    'hot_file' => public_path('../frontend/dist/hot'),

    /*
    |--------------------------------------------------------------------------
    | Frontend URL
    |--------------------------------------------------------------------------
    |
    | The URL where the frontend application is served. This is used for
    | CORS configuration to allow cross-origin requests from the frontend.
    |
    */

    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),

];
