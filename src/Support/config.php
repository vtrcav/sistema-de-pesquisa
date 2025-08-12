<?php

return [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=basehosp;charset=utf8',
        'user' => 'root',
        'pass' => '',
        'options' => [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
            PDO::ATTR_PERSISTENT => true,
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
        ],
    ],
];


