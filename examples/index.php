<?php

require(__DIR__ . '/../vendor/autoload.php');

$router = new \Trulyao\PhpRouter\Router(__DIR__."/views", "examples");

$router->get('/', function($request, $response) {
    return $response->send("Hello World from static GET! <br/> Name: {$request->query('name')}")->status(200);
});

$router->get('/second', function($request, $response) {
    return $response->use("second.php")->status(200);
});

$router->post('/', function ($request, $response) {
    return $response->json(["name" => $request->body("name")])->status(200);
});

$router->get('/dynamic/:id', function($id) {
    echo "Dynamic GET route with id: $id";
});

$router->serve();