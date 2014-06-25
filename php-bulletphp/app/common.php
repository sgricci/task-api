<?php

$settings = array(
    'driver' => 'mysql',
    'host' => '127.0.0.1',
    'database' => 'todoapp',
    'username' => 'root',
    'password' => 'root',
    'collation' => 'utf8_general_ci',
    'prefix' => ''
);

// Bootstrap Eloquent ORM
$pdo = new PDO('mysql:host='.$settings['host'].';dbname='.$settings['database'], $settings['username'], $settings['password']);
$conn = new \Illuminate\Database\MySqlConnection($pdo, 'todoapp');
$resolver = new \Illuminate\Database\ConnectionResolver();
$resolver->addConnection('default', $conn);
$resolver->setDefaultConnection('default');
\Illuminate\Database\Eloquent\Model::setConnectionResolver($resolver);