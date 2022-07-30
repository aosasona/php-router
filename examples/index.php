<?php

require(__DIR__ . '/../vendor/autoload.php');

$router = new \Trulyao\PhpRouter\Router(__DIR__."/views", "examples");

/**
 * @desc Simple index route
 * @route /
 */
$router->get('/', function($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: static GET! </br> Query(name): {$req->query('name')}")->status(200);
});


/**
 * @desc Serving a view/using a controller
 * @route /use
 */
$router->get('/use', function($req, $res) {
    return $res->use("second.php")->status(200);
});
/**
 * @desc Responding with JSON
 * @route /json
 */
$router->get('/json', function($req, $res) {
    return $res->send(["name" => "Hello"])->status(200);
});

/**
 * @desc [GET] Single dynamic route
 * @route /dynamic/:id
 */
$router->get('/dynamic/:id', function($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: dynamic GET! </br> Params(ID): {$req->params("id")}")->status(200);
});

/**
 * @desc [GET] Nested dynamic route
 * @route /dynamic/:id/nested
 */
$router->get('/dynamic/:id/nested', function($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: dynamic nested GET! </br> Params(ID): {$req->params("id")}")->status(200);
});

/**
 * @desc [GET] Redirecting to another route
 * @route /redirect
 */
$router->get("/redirect", function($req, $res) {
    return $res->redirect("/examples/dynamic/1");
});

/**
 * @desc [POST] Single POST route
 * @route /
 */
$router->post('/', function ($req, $res) {
    return $res->send(["name" => $req->body("name"), "method" => "POST"])->status(200);
});

/**
 * @desc [POST] single POST dynamic route
 * @route /:id
 */
$router->post('/:id', function ($req, $res) {
    return $res->send(["id" => $req->params("id")])->status(200);
});

/**
 * @desc [PUT] Single dynamic PUT route
 * @route /:id
 */
$router->put('/:id', function ($req, $res) {
    return $res->send(["method" => "PUT"])->status(200);
});

/**
 * @desc [DELETE] Single dynamic DELETE route
 * @route /:id
 */
$router->delete('/:id', function ($req, $res) {
    return $res->send(["method" => "DELETE"])->status(200);
});

// Start the router
$router->serve();