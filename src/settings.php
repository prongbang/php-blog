<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // Database
        'db' => [
            'driver' => 'mysql',
            'host' => 'localhost',
            'database' => 'lorjulac_buffer',
            
            //// dev
            // 'username' => 'root',
            // 'password' => 'Stringbufferx64',
            //// prod
            'username' => 'lorjulac_0x9a',
            'password' => 'n5cbC0iETz',
            'charset'   => 'utf8',
            'collation' => 'utf8_unicode_ci',
            'prefix'    => '',
        ],

        // Renderer settings
        'renderer' => [
            'template_path' => __DIR__ . '/../templates/',
        ],

        // cache
        'cache' => [
            'cache_path' => __DIR__ . '/../path/to/cache/',
        ],

        // Monolog settings
        'logger' => [
            'name' => 'lorjula',
            'path' => __DIR__ . '/../logs/app.log',
            'level' => \Monolog\Logger::DEBUG,
        ],
    ],
];
