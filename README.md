# PHP Router

PHP-Router is a modern, fast, and adaptable composer package that provides express-style routing in PHP without a framework.

This website is powered by this package -> [View site](https://phprouter.herokuapp.com/)

## Installation

```bash
composer require trulyao/php-router
```

Create a new dockerized PHP project (contains MySQL, Apache2, PHPMyAdmin) that uses this package by running this Composer command:

```bash
composer create-project trulyao/php-starter hello-world
```

### Update .htaccess file

This is very important to get the router working correctly.
> Warning: Depending on your Apache configuration, some headers will not be allowed to come through to your application, this has nothing to do with this package, you just need to enable them in your Apache configuration.


```
Options +FollowSymLinks
RewriteEngine On
RewriteCond %{HTTP:Authorization} ^(.*)
RewriteCond %{REQUEST_URI} !=/index.php
RewriteCond %{REQUEST_URI} !.*\.png$ [NC]
RewriteCond %{REQUEST_URI} !.*\.jpg$ [NC]
RewriteCond %{REQUEST_URI} !.*\.css$ [NC]
RewriteCond %{REQUEST_URI} !.*\.gif$ [NC]
RewriteCond %{REQUEST_URI} !.*\.js$ [NC]
RewriteRule .* /index.php
RewriteRule .* - [E=HTTP_CONTENT_TYPE:%{HTTP:Content-Type},L]
RewriteRule .* - [e=HTTP_AUTHORIZATION:%1]
```


## Features

- Open source
- Fast and easy-to-use syntax
- Provides request and response objects
- Supports dynamic routing like `/:id` or `/:id/:name`
- Supports 4 **major** HTTP methods (GET, POST, PUT, DELETE)
- Uses callbacks to handle requests
- Supports custom 404 and 500 error pages
- Supports redirection
- Serves static files, JSON and more
- Helper functions for common tasks like sending JSON, setting status codes etc.
- Function and class based controller and middleware support

## Usage

`index.php`

```php
<?php

use \Trulyao\PhpRouter\Router as Router;

$router = new Router(__DIR__."/views", "demo");

$router->get("/", function($req, $res) {
    return $res->send("<h1>Hello World</h1>")
               ->status(200);
});

$router->get('/render', function ($req, $res) {
    return $res->render("second.html", $req);
});

$router->post("/", function($req, $res) {
   return $res->send([
       "message" => "Hello World"
   ]);
});

# using a class based controller
$router->delete("/", [new NoteController(), "destroy"]); 

$router->route("/chained")
    ->get(function ($req, $res) {
    return $res->send("GET - Chained!");
    })
    ->post(function ($req, $res) {
    return $res->send("POST - Chained!");
    });

# Start the router - very important!
$router->serve();

?>
```

`/views` - The directory where your views/controllers/source files are located.

`/demo` - This is the base URL for your application eg. `api` for `/api/*` or `v1` for `/v1/*`.

## The `$req` object

The `$req` object contains the request data, it also has helper methods for accessing this data.

- `query("name")` - Returns the query string value for the given name or all query string values if no name is given.

- `body("name")` - Returns the body value for the given name or all body values if no name is given.

- `params("name")` - Returns the params value for the given name or all file values if no name is given.
- `path()` - Get current full request path.
- `headers()` - Get all request headers.
- `header("name")` - Get a single request header value.
- `append($key, $value)` - Append data to the request object.
- `data` - The data array for the request object (useful for passing data to the middleware and callback functions).

> Note: The `$req` object is passed to all middleware and callback functions. If you use any of the response methods like `send()`, the middleware will not move on to the next one.

## The `$res` object

The `$res` object is used to control the response and holds data related to responses and just like the request object, it has methods you can use.

- `error($message, $status)` - Send a JSON error response.
- `send($data)` - Send a JSON/HTML response; automatically detected.
- `json($data)` - Send a JSON response.
- `render($file)` - Render a view with the built-in mini template engine, you can also pass in your own data.
- `redirect($path)` - Redirect to another path - eg. `/example/login`
- `status($status)` - Set the status code (defaults to 200, optional)
- `use_engine()` - Enable and use the built-in mini template engine for rendering views.


> More methods will also be added in the future.

You can access these any functions outside your index or main file too by using the namespace `Trulyao\PhpRouter\HTTP`. Also, you are not constrained to any particular coding style or variable naming convention either.

## Error Pages

You can easily add a custom 404, 405 or 500 page by creating a file in the `/views` directory (or wherever your base path is; where your controllers or views are) called `404.php`, `405.php` or `500.php`

## Views & Templates
In views, you are provided with the following variables by default:
- `query` -  The query string values.
- `body` - The request body values.
- `params` - The request params values.
- `headers` - The request headers.
- `data` - User-defined data eg. current user from JWT
- `root_dir` - The current request path.
- 
> Note: headers are made case-insensitive while accessing them in views.
This package also comes with a templating engine that is turned off by default, you can enable or disable it by passing a boolean value to the `render` method. This templating engine is still experimental and does not support a lot of features yet. Check [this](/examples/views/middleware_view.html) file for an example of how to use it.

You can use any of these variables in a PHP view file by using $query[\'field_name'\], $data[\'field_name'\] etc or in a template file (most likely html) by using the directives like @query('field_name'), @body().

### Components and Raw Code
You can execute some PHP codes in views by using the `@php ... @endphp` directives. This is quite limited and useful for codes that don't echo anything like the `session_start()` function as all the output is at the top of the view file.


```html
@php
    session_start();
@endphp
```

You can also use the `@component` directive to include a component file.

```html
@component('component.html')
```

For more examples, check out the [examples](/examples) directory. If you have forked and/or cloned this repo; to start the test server, run `composer run start:dev`, the base URL is `http://localhost:20000/demo`. Here are all the available endpoints:
- [GET] `/`
- [GET] `/render`
- [GET] `/render/middleware`
- [GET] `/json`
- [GET] `/dynamic/:id`
- [GET] `/dynamic/:id/nested`
- [GET] `/dynamic/:id/:name`
- [GET] `/middleware`
- [GET] `/redirect`
- [POST] `/:id`
- [PUT] `/:id`
- [DELETE] `/:id`
- [GET | POST | PUT | DELETE] `/chained`

## Contribute

- Fork the repository on GitHub
- Add your own code
- Create a pull request with your changes highlighted
- For more information, contact me [here](https://twitter.com/trulyao)

> This documentation will be updated as soon as new features and methods are added to the package.
> 
> Warning: PHP, like a lot of other languages, runs from top to bottom, to avoid conflicts, put your chained routes at the bottom of the file; it is still fairly unstable and may override your dynamic routes eg. putting `/chained` which is a chained route before `/:id` for a GET request will only direct you to the `/chained` route because it technically matches the latter.
