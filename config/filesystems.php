<?php

use App\Constants\DiskNames;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    |
    | Here you may specify the default filesystem disk that should be used
    | by the framework. The "local" disk, as well as a variety of cloud
    | based disks are available to your application for file storage.
    |
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | Below you may configure as many filesystem disks as necessary, and you
    | may even configure multiple disks for the same driver. Examples for
    | most supported storage drivers are configured here for reference.
    |
    | Supported drivers: "local", "ftp", "sftp", "s3"
    |
    */

    'disks' => [

        'local' => [
            'driver' => 'local',
            'root' => storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
        ],

        'public' => [
            'driver' => 'local',
            'root' => storage_path('app/public'),
            'url' => env('APP_URL') . '/storage',
            'visibility' => 'public',
            'throw' => false,
            'report' => false,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
            'public' => true,  // Default to true

        ],
        DiskNames::SUBAPASEPUBLIC->name => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('AWS_BUCKET_PUBLIC'),
            'url' => env('AWS_URL_PUBLIC'),
            'endpoint' => env('AWS_ENDPOINT_PUBLIC'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true), // Important change
            'visibility' => 'public', // If you want files publicly accessible
        ],
        DiskNames::DRIVERS_PROFILE->name => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('AWS_BUCKET_PUBLIC'),
            'url' => env('AWS_URL_PUBLIC'),
            'endpoint' => env('AWS_ENDPOINT_PUBLIC'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true), // Important change
            'visibility' => 'public', // If you want files publicly accessible
            'root' => DiskNames::DRIVERS_PROFILE->value,
        ],
        DiskNames::DRIVERS_PROFILE->name => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('AWS_BUCKET_PUBLIC'),
            'url' => env('AWS_URL_PUBLIC'),
            'endpoint' => env('AWS_ENDPOINT_PUBLIC'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true), // Important change
            'visibility' => 'public', // If you want files publicly accessible
            'root' => DiskNames::DRIVERS_PROFILE->value,
        ],
        DiskNames::DRIVERS_LICENSE->name => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('AWS_BUCKET_PUBLIC'),
            'url' => env('AWS_URL_PUBLIC'),
            'endpoint' => env('AWS_ENDPOINT_PUBLIC'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true), // Important change
            'visibility' => 'public', // If you want files publicly accessible
            'root' => DiskNames::DRIVERS_LICENSE->value,
        ],
        DiskNames::DRIVERS_CAR_PHOTOS->name => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('AWS_BUCKET_PUBLIC'),
            'url' => env('AWS_URL_PUBLIC'),
            'endpoint' => env('AWS_ENDPOINT_PUBLIC'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true), // Important change
            'visibility' => 'public', // If you want files publicly accessible
            'root' => DiskNames::DRIVERS_CAR_PHOTOS->value,
        ],
        DiskNames::SYSTEM->name => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => 'auto',
            'bucket' => env('AWS_BUCKET_PUBLIC'),
            'url' => env('AWS_URL_PUBLIC'),
            'endpoint' => env('AWS_ENDPOINT_PUBLIC'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true), // Important change
            'visibility' => 'public', // If you want files publicly accessible
            'root' => DiskNames::SYSTEM->value, // Dedicated folder for agents

        ],
        // 'supabase_private' => [
        //     'driver' => 's3',
        //     'key' => env('AWS_ACCESS_KEY_ID'),
        //     'secret' => env('AWS_SECRET_ACCESS_KEY'),
        //     'region' => env('AWS_DEFAULT_REGION'),
        //     'bucket' => env('AWS_BUCKET_PRIVATE'),
        //     'endpoint' => env('AWS_ENDPOINT_PRIVATE'),
        //     'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', true),
        //     'public' => false,  // Default to true
        // ],
        DiskNames::SUPABASEPRIVATE->name => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'), // Use your service role key here
            'region' => 'auto',
            'bucket' => env('AWS_BUCKET_PRIVATE'),
            'endpoint' =>  env('AWS_ENDPOINT_PRIVATE'),
            'use_path_style_endpoint' =>  env('AWS_USE_PATH_STYLE_ENDPOINT', true),
            'visibility' => 'private',
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    |
    | Here you may configure the symbolic links that will be created when the
    | `storage:link` Artisan command is executed. The array keys should be
    | the locations of the links and the values should be their targets.
    |
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
