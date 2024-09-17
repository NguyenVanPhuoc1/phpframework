<?php 
    return[
        'basePath' => '/my-framework-php/public',
        'rootDir' => dirname(__DIR__),//lay duong dan file gốc
        'layout' => 'index',
        'db' => [
            // 'host' => '127.0.0.1', //nếu host là vitualhost
            'DB_NAME'=> 'phpshop',
            'DB_HOST' => 'localhost',
            'PORT' => 3306,
            'DB_USER' => 'root',
            'DB_PASSWORD' => '',
            'DB_CHARSET'=> 'utf8',
        ],
    ];
?>