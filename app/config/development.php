<?php

date_default_timezone_set('Europe/Kiev');

return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production

        'view' => [
            'template_path'         => APP . 'frontend/views/',
            'template_path_backend' => APP . 'backend/views/',
            'twig'                  => [
                'cache' => false, //ROOT . 'cache/twig',
                'debug' => true,
                'auto_reload' => true,
            ],
        ],

        // Languqges
        'language_dir' => APP . 'backend/languages/',

        // Images Folder (public/frontend/img)
        'images_dir' => 'frontend/img/',

        // Monolog settings
        'logger' => [
            'name' => 'slim-app',
            'path' => ROOT . 'logs/app.log',
        ],

        // DB
        'db' => [
            'driver'    => 'mysql',
            'host'      => 'localhost',
            'database'  => 'slim-blog-dev.loc',
            'username'  => 'root',
            'password'  => 'root',
            'prefix'    => '',
            'charset'   => 'utf8',
            'collation' => 'utf8_general_ci',
        ],

        // Hash settings
        'hash' => [
            'algo' => PASSWORD_BCRYPT,
            'cost' => 10,
        ],

        // Auth Settings
        'auth' => [
            'session'  => 'user_id',
            'remember' => 'user_r',
        ],
    ],
];
