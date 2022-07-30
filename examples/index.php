<?php

require(__DIR__ . '/../vendor/autoload.php');

$router = new \Trulyao\PhpRouter\Router(__DIR__."/views", "examples");

$router->get('/', function($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: static GET! </br> Query(name): {$req->query('name')}")->status(200);
});

$router->get('/use', function($req, $res) {
    return $res->use("second.php")->status(200);
});

$router->get('/json', function($req, $res) {
    return $res->send(["name" => "Hello"])->status(200);
});

$router->post('/', function ($req, $res) {
    return $res->send(["name" => $req->body("name")])->status(200);
});

$router->get('/dynamic/:id', function($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: dynamic GET! </br> Params(ID): {$req->params("id")}")->status(200);
});

$router->get('/dynamic/:id/nested', function($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: dynamic nested GET! </br> Params(ID): {$req->params("id")}")->status(200);
});

$router->serve();