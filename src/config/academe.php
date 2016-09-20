<?php

return [

    'default_connection' => 'mysql',

    'connections' => [

        'mysql' => [
            'type'        => 'mysql',
            'prefix'      => '',
            'host'        => '127.0.0.1',
            'port'        => 3306,
            'username'    => 'username',
            'password'    => 'password',
            'database'    => 'db',
            'charset'     => 'utf8',
            'collation'   => 'utf8_unicode_ci',
            'pdo_options' => [
//                \PDO::ATTR_EMULATE_PREPARES => true,
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