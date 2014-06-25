<?php
define('BULLET_ROOT', dirname(__DIR__));
define('BULLET_APP_ROOT', BULLET_ROOT . '/app/');
define('BULLET_SRC_ROOT', BULLET_APP_ROOT . '/src/');
 
// Composer Autoloader
$loader = require BULLET_ROOT . '/vendor/autoload.php';
 
// Bullet App
$app = new Bullet\App();
$request = new Bullet\Request();
 
// Common include
require BULLET_APP_ROOT . '/common.php';
 
// Require all paths/routes
$routesDir = BULLET_APP_ROOT . '/routes/';
require $routesDir . 'list.php';
require $routesDir . 'task.php';
 
// Response
echo $app->run($request);