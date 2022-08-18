<?php

require(__DIR__ . '/../vendor/autoload.php');

use Trulyao\PhpRouter\Router as Router;

$router = new Router(__DIR__ . "/views", "demo");


/**
 * @desc Simple index route
 * @route /
 */
$router->get('/', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> <br/> Source: static GET </br> Query(name): {$req->query('name')}")->status(200);
});


/**
 * @desc Serving a view/using a controller
 * @route /use
 */
$router->get('/render', function ($req, $res) {
    return $res->render("second.php")->status(200);
});

/**
 * @desc Responding with JSON
 * @route /json
 */
$router->get('/json', function ($req, $res) {
    return $res->send(["name" => "Hello"])->status(200);
});

/**
 * @desc [GET] Single dynamic route
 * @route /dynamic/:id
 */
$router->get('/dynamic/:id', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: dynamic GET! </br> Params(ID): {$req->params("id")}")->status(200);
});

/**
 * @desc [GET] Mixed dynamic route
 * @route /dynamic/:id/nested
 */
$router->get('/dynamic/:id/nested', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: dynamic nested GET! </br> Params(ID): {$req->params("id")} </br> Path: {$req->path()}")->status(200);
});

/**
 * @desc [GET] Nested dynamic route
 * @route /dynamic/:id/:name
 */
$router->get('/dynamic/:id/:name', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> Source: dynamic nested GET! </br> Param(ID): {$req->params("id")} </br> Param(Name): {$req->params("name")}")->status(200);
});

/**
 * @desc Using middlewares
 * @route /middleware
 */
$router->get('/middleware', function ($req, $res) {
    $req->append("name", "John");// Appending data to the request object
}, function ($req, $res) {
    $req->append("age", 16); // Appending more data to the request object
    $res->send("From middleware 2 <br/> 
            Name from previous middleware: {$req->data["name"]} 
            </br> ------ </br>");
}, function ($req, $res) {
    return $res->send("FROM FINAL CALLBACK </br> 
                Name: {$req->data["name"]}</br> 
                Age: {$req->data["age"]}")->status(200);
});


/**
 * @desc Using chained routes
 * @route /chained
 */
$router->route("/chained")
    ->get(function ($req, $res) {
    return $res->send("GET - Chained!")->status(200);
    })
    ->post(function ($req, $res) {
    return $res->send("GET - Chained!")->status(200);
    });


/**
 * @desc [GET] Redirecting to another route
 * @route /redirect
 */
$router->get("/redirect", function ($req, $res) {
    return $res->redirect("/examples/dynamic/1");
});

/**
 * @desc [POST] Single POST route
 * @route /
 */
$router->post('/', function ($req, $res) {
    return $res->send(["name" => $req->body("name"), "method" => "POST"]);
});

/**
 * @desc [POST] single POST dynamic route
 * @route /:id
 */
$router->post('/:id', function ($req, $res) {
    return $res->send(["id" => $req->params("id")]);
});

/**
 * @desc [PUT] Single dynamic PUT route
 * @route /:id
 */
$router->put('/:id', function ($req, $res) {
    return $res->send(["method" => "PUT"]);
});

/**
 * @desc [DELETE] Single dynamic DELETE route
 * @route /:id
 */
$router->delete('/:id', function ($req, $res) {
    return $res->send(["method" => "DELETE"]);
});

// Start the router
$router->serve();
