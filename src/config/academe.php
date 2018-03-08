<?php

return [
    // If a Blueprint doesn't specified a connection, this connection will be used.
    'default_connection' => 'mysql',

    'connections' => [
        // MySQL connection config.
        'mysql'   => [
            'type'        => 'mysql',
            'prefix'      => env('DB_PREFIX', ''),
            'host'        => env('DB_HOST', 'localhost'),
            'port'        => env('DB_PORT', 3306),
            'username'    => env('DB_USERNAME', 'forge'),
            'password'    => env('DB_PASSWORD', ''),
            'database'    => env('DB_DATABASE', ''),
            'charset'     => env('DB_CHARSET', 'utf8'),
            'collation'   => env('DB_COLLATION', 'utf8_unicode_ci'),

            // You can specify PDO options here.
            //
            'pdo_options' => [
                // Here are some commonly used PDO options:
                //
                // \PDO::ATTR_EMULATE_PREPARES   => true,
                // \PDO::MYSQL_ATTR_INIT_COMMAND => "set session sql_mode='NO_ENGINE_SUBSTITUTION'",
            ],
        ],
        // If you don't use MongoDB right now, you can remove this block.
        'mongodb' => [
            'type'           => 'mongodb',
            // You must write all MongoDB instances in a replica here.
            'instances'      => [
                [
                    'host' => env('MONGODB_HOST', '127.0.0.1'),
                    'port' => env('MONGODB_PORT', 27017),
                ],
            ],
            'authentication' => [
                'username' => env('MONGODB_USERNAME', ''),
                'password' => env('MONGODB_PASSWORD', ''),
            ],
            'database'       => env('MONGODB_DATABASE', 'default'),
            // If the authentication isn't authenticated against the database
            // that actually been used, you can provider another database only
            // for authentification.
            //
            // 'authenticationDatabase' => 'auth',
            'replica'        => env('MONGODB_REPLICA', null),
            // More options for MongoDB extension.
            'options'        => [
                // Here are some commonly used MongoDB connection options:
                //
                // 'w'          => 'majority',
                // 'j'          => true,
                // 'wtimeoutMS' => 6000,
                // 'ssl'        => false,
            ],
        ],
    ],

    // Configs that only take effects when you use with Laravel framework.
    'laravel' => [
        // Relative to app_path()
        'blueprint_directory' => 'Blueprints',
    ],
];