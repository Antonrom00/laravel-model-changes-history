<?php

use Antonrom\ModelChangesHistory\Models\Change;

return [

    /*
    |--------------------------------------------------------------------------
    | Global Recording Model Changes History
    |--------------------------------------------------------------------------
    |
    | Supported: true or false
    |
    */

    'record_changes_history' => env('RECORD_CHANGES_HISTORY', true),


    /*
    |--------------------------------------------------------------------------
    | Record Model Changes History Using Only For Debugging
    |--------------------------------------------------------------------------
    |
    | Supported: true or false
    |
    */

    'use_only_for_debug' => true,


    /*
    |--------------------------------------------------------------------------
    | Recording Stack Trace in DB
    |--------------------------------------------------------------------------
    |
    | This option controls the recording stack trace for getting Class, Function and Line of calling code
    |
    | Supported: true or false
    |
    */

    'record_stack_trace' => true,


    /*
    |--------------------------------------------------------------------------
    | Ignoring actions of CRUD
    |--------------------------------------------------------------------------
    |
    | This option controls the ignoring of recording changes types
    |
    */

    'ignored_actions' => [
        // Change::TYPE_CREATED,
        // Change::TYPE_UPDATED,
        // Change::TYPE_DELETED,
        // Change::TYPE_RESTORED,
        // Change::TYPE_FORCE_DELETED,
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Model Changes History Store
    |--------------------------------------------------------------------------
    |
    | This option controls the default model changes history connection that gets used while
    | using this library.
    |
    | Supported: "database", "file", "redis"
    |
    */

    'storage' => env('MODEL_CHANGES_HISTORY_STORAGE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Model Changes History Stores
    |--------------------------------------------------------------------------
    |
    | Here you may define all of the model changes history "stores" for your application as
    | well as their drivers.
    |
    */

    'stores' => [

        'database' => [
            'driver'     => 'database',
            'table'      => 'model_changes_history',
            'connection' => null,
        ],

        'file' => [
            'driver'    => 'file',
            'disk'      => 'model_changes_history',
            'file_name' => 'changes_history.txt',

            // This disk used as a default
            'model_changes_history' => [
                'driver' => 'local',
                'root'   => storage_path('app/model_changes_history'),
            ],
        ],

        'redis' => [
            'driver'     => 'redis',
            'key'        => 'model_changes_history',
            'connection' => 'model_changes_history',

            // This connection used as a default
            'model_changes_history' => [
                'host'     => env('REDIS_HOST', '127.0.0.1'),
                'password' => env('REDIS_PASSWORD', null),
                'port'     => env('REDIS_PORT', 6379),
                'database' => env('REDIS_DB', 0),
            ],
        ],

    ],

];
