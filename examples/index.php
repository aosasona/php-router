<?php

require(__DIR__ . '/../vendor/autoload.php');

use Trulyao\PhpRouter\HTTP\Request;
use Trulyao\PhpRouter\HTTP\Response;
use Trulyao\PhpRouter\Router as Router;

$router = new Router(__DIR__ . "/views", "demo");


/**
 * @desc Simple index route
 * @route /
 */
$router->get('/', function (Request $req, Response $res) {
    return $res->send("<h1>Hello World</h1> <br/> 
                Source: static GET </br> 
                Query(name): {$req->query('name')}")->status(200);
});


/**
 * @desc Serving a view
 * @route /render
 */
$router->get('/render', function (Request $req, Response $res) {
    return $res->render("second.php", $req);
});

/**
 * @desc Serving a view with middleware
 * @route /render/middleware
 */
$router->get('/render/middleware', function ($req) {
    $req->append('name', 'Bob');
    $req->append('more', ["first_name" => "Joe", "last_name" => "Zhang"]);
    $req->append('hobbies', ["watching youtube", "sleeping", "coding"]);
}, function (Request $req, Response $res) {
    return $res->use_engine()
                ->render("middleware_view.html", $req);
});

/**
 * @desc Responding with JSON
 * @route /json
 */
$router->get('/json', function (Request $req, Response $res) {
    return $res->send(["name" => "Hello"])->status(200);
});

/**
 * @desc [GET] Single dynamic route
 * @route /dynamic/:id
 */
$router->get('/dynamic/:id', function (Request $req, Response $res) {
    return $res->send("<h1>Hello World</h1> </br> 
                Source: dynamic GET! </br> 
                Params(ID): {$req->params("id")}")->status(200);
});

/**
 * @desc [GET] Mixed dynamic route
 * @route /dynamic/:id/nested
 */
$router->get('/dynamic/:id/nested', function (Request $req, Response $res) {
    return $res->send("<h1>Hello World</h1> </br> 
                Source: dynamic nested GET! </br> 
                Params(ID): {$req->params("id")} </br> 
                Path: {$req->path()}")->status(200);
});

/**
 * @desc [GET] Nested dynamic route
 * @route /dynamic/:id/:name
 */
$router->get('/dynamic/:id/:name', function (Request $req, Response $res) {
    return $res->send("<h1>Hello World</h1> 
            </br> Source: dynamic nested GET! </br> 
            Param(ID): {$req->params("id")} </br> 
            Param(Name): {$req->params("name")}")->status(200);
});

/**
 * @desc Using middlewares
 * @route /middleware
 */
$router->get('/middleware', function (Request $req, Response $res) {
    $req->append("name", "John");// Appending data to the request object
}, function (Request $req, Response $res) {
    $req->append("age", 16); // Appending more data to the request object
}, function (Request $req, Response $res) {
    return $res->send("FROM FINAL CALLBACK </br> 
                Name: {$req->data["name"]}</br> 
                Age: {$req->data["age"]}")->status(200);
});


/**
 * @desc [GET] Redirecting to another route
 * @route /redirect
 */
$router->get("/redirect", function (Request $req, Response $res) {
    return $res->redirect("/examples/dynamic/1");
});

/**
 * @desc [POST] Single POST route
 * @route /
 */
$router->post('/', function (Request $req, Response $res) {
    return $res->send(["name" => $req->body("name"), "method" => "POST"]);
});

/**
 * @desc [POST] single POST dynamic route
 * @route /:id
 */
$router->post('/:id', function (Request $req, Response $res) {
    return $res->send(["method" => "POST", "id" => $req->params("id")]);
});

/**
 * @desc [PUT] Single dynamic PUT route
 * @route /:id
 */
$router->put('/:id', function (Request $req, Response $res) {
    return $res->send(["method" => "PUT", "id" => $req->params("id")]);
});

/**
 * @desc [DELETE] Single dynamic DELETE route
 * @route /:id
 */
$router->delete('/:id', function (Request $req, Response $res) {
    return $res->send(["method" => "DELETE", "id" => $req->params("id")]);
});


/**
 * @desc Using chained routes
 * @route /chained
 */
$router->route("/chained")
    ->get(function (Request $req, Response $res) {
        return $res->send("GET - Chained!")->status(200);
    })
    ->post(function (Request $req, Response $res) {
        return $res->send("POST - Chained!")->status(200);
    })
    ->put(function (Request $req, Response $res) {
        return $res->send("PUT - Chained!")->status(200);
    })
    ->delete(function (Request $req, Response $res) {
        return $res->send("DELETE - Chained!")->status(200);
    });

// Start the router
$router->serve();
