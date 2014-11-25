<?php

return [
    'user' => [
        'prefix' => '@user_',
        'expired' => 8*60*60,
    ],
    'messages' => [
        'total' => 50,
        'amount_full_messages' => 50 - 30,
        'amount_chars_old_messages' => 5,
    ],
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=mydb',
        'username' => 'admin',
        'password' => '111',
        'options' => [
            PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES \'UTF8\''
        ],
    ]
];
