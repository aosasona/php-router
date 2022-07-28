<?php

require(__DIR__ . '/../vendor/autoload.php');

$router = new \Trulyao\PhpRouter\Router(__DIR__."/views");

//var_dump($router);

$router->get('/', 'index.php');