<?php

return [

    'default_connection' => 'mysql',

    'connections' => [

        'mysql' => [
            'type'        => 'mysql',
            'prefix'      => '',
            'host'        => env('DB_HOST', 'localhost'),
            'port'        => env('DB_PORT', 3306),
            'username'    => env('DB_USERNAME', 'forge'),
            'password'    => env('DB_PASSWORD', ''),
            'database'    => 'db',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'pdo_options' => [
//                \PDO::ATTR_EMULATE_PREPARES => true,
//                \PDO::MYSQL_ATTR_INIT_COMMAND => "set session sql_mode='NO_ENGINE_SUBSTITUTION'",
            ],
        ],

        'mongodb' => [
            'type'           => 'mongodb',
            'instances'      => [
                [
                    'host' => '127.0.0.1',
                    'port' => 27017,
                ],
            ],
            'authentication' => [
                'username' => 'username',
                'password' => 'password',
            ],
            'database'       => 'mongodb',
            'options'        => [
//                'replicaSet' => 'my_replica',
//                'w'          => 2, // 'majority'
//                'j'          => true,
//                'wtimeoutMS' => 7000,
//                'ssl'        => false,
            ],
        ],

    ],

];