<?php

require_once('middleware/test.php');
use codad5\examples\middleware\jwtT;
use Trulyao\PhpRouter\Router as Router;

$jwt =  "hiigidhsd";
$router = new Router(__DIR__ . "/views", "examples", '/dynamic');



/**
 * @desc [GET] Single dynamic route
 * @route /dynamic/:id
 */
$router->get('/:id', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> 
                Source: dynamic GET! </br> 
                Params(ID): {$req->params("id")}")->status(200);
});

/**
 * @desc [GET] Mixed dynamic route
 * @route /dynamic/:id/nested
 */
$router->get('/:id/nested', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> </br> 
                Source: dynamic nested GET! </br> 
                Params(ID): {$req->params("id")} </br> 
                Path: {$req->path()}")->status(200);
});

/**
 * @desc [GET] Nested dynamic route
 * @route /dynamic/:id/:name
 */
$router->get('/:id/:name', function ($req, $res) {
    return $res->send("<h1>Hello World</h1> 
            </br> Source: dynamic nested GET! </br> 
            Param(ID): {$req->params("id")} </br> 
            Param(Name): {$req->params("name")}")->status(200);
});


$export = $router;