<?php

require(__DIR__ . '/../vendor/autoload.php');

$router = new \Trulyao\PhpRouter\Router(__DIR__."/views", "examples");

//var_dump($router);

$router->get('/', 'main.php');
$router->get('/second', 'second.php');
$router->post('/', 'third.php');

$router->run();