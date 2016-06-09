<?php
require 'vendor/autoload.php';
require 'config.php';

$app = new \Slim\Slim();

// ルーティングの読み込み
$routers = glob('./routes/*.router.php');
foreach ($routers as $router) {
    require $router;
}

$app->run();