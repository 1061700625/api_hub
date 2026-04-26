<?php

return [
    'app' => [
        'name' => '小锋学长的API Hub',
        'base_url' => '',
        'debug' => true,
    ],
    'db' => [
        'dsn' => 'sqlite:' . __DIR__ . '/database/database.sqlite',
    ],
    'admin' => [
        'default_username' => 'admin',
        'default_password' => 'admin123',
    ],
];
