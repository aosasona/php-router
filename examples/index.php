<?php

require(__DIR__ . '/../vendor/autoload.php');
require('middleware/test.php');
use codad5\examples\middleware\jwtT;
use Codad5\PhpInex\Import as Import;
use Trulyao\PhpRouter\Router as Router;

$jwt = new jwtT('helloworld');
$router = new Router(__DIR__ . "/views", "");

## using codad5\php-inex to import sub - routes
$dynamic_routes = Import::this('./Routes/Dynamic');
$router->use_route($dynamic_routes);


## using include_once or require_once to import sub - routes

#================== Uncomment this block to try the use of require

// $shop_route = require_once('Routes/Shop.php');
// $router->use_route($shop_route);

#===================End

## due to the use of include_once / require_once $jwt is been overrided by the $jwt in Routes/Shop.php


var_dump($jwt);


/**
 * @desc Simple index route
 * @route /
 */
$router->get('/', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> <br/> 
                Source: static GET </br> 
                Query(name): {$req->query('name')}")->status(200);
});


/**
 * @desc Serving a view
 * @route /render
 */
$router->get('/render', [$jwt, 'verify'], function ($req, $res) {
    // return $res->render("second.php", $req);
    echo "hello world";
});



$router->get('/testjwt', [$jwt, 'create'], function ($req, $res) {
    return $res->send(['jwt' => $req->data])->status(200);
});

/**
 * @desc Serving a view with middleware
 * @route /render/middleware
 */
$router->get('/render/middleware', function ($req) {
    $req->append('name', 'Trulyao');
    $req->append('more', ["first_name" => "Joe", "last_name" => "Zhang"]);
}, function ($req, $res) {
    return $res->render("middleware_view.php", $req);
});

/**
 * @desc Responding with JSON
 * @route /json
 */
$router->get('/json', function ($req, $res) {
    return $res->send(["name" => "Hello"])->status(200);
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
    return $res->send("POST - Chained!")->status(200);
    })
    ->put(function ($req, $res) {
    return $res->send("PUT - Chained!")->status(200);
    })
    ->delete(function ($req, $res) {
    return $res->send("DELETE - Chained!")->status(200);
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
