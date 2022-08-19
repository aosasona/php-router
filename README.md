# PHP Router

PHP-Router is a modern, fast, and adaptable composer package that provides express-style routing in PHP.

This website is powered by this package -> [View site](https://phprouter.herokuapp.com/)

## Installation

```bash
composer require trulyao/php-router
```

### Update .htaccess file

This is very important to get the router working correctly.


```
Options +FollowSymLinks
RewriteEngine On
RewriteRule .* - [E=HTTP_CONTENT_TYPE:%{HTTP:Content-Type},L]
RewriteCond %{REQUEST_URI} !=/index.php
RewriteCond %{REQUEST_URI} !.*\.png$ [NC]
RewriteCond %{REQUEST_URI} !.*\.jpg$ [NC]
RewriteCond %{REQUEST_URI} !.*\.css$ [NC]
RewriteCond %{REQUEST_URI} !.*\.gif$ [NC]
RewriteCond %{REQUEST_URI} !.*\.js$ [NC]
RewriteRule .* /index.php
```

You could also download these files and use them directly.

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

require(__DIR__ . '/vendor/autoload.php');

use \Trulyao\PhpRouter\Router as Router;

$router = new Router(__DIR__."/views", "demo");

$router->get("/", function($req, $res) {
    return $res->send("<h1>Hello World</h1>")
               ->status(200);
});

$router->post("/", function($req, $res) {
   return $res->send([
       "message" => "Hello World"
   ]);
});

$router->delete("/", [new NoteController(), "destroy"]);

$router->route("/chained")
    ->get(function ($req, $res) {
    return $res->send("GET - Chained!")->status(200);
    })
    ->post(function ($req, $res) {
    return $res->send("POST - Chained!")->status(200);
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

> More methods will be added in the future.

## The `$res` object

The `$res` object is used to control the response and holds data related to responses and just like the request object, it has methods you can use.

- `error($message, $status)` - Send a JSON error response.
- `send($data)` - Send a JSON/HTML response; automatically detected.
- `json($data)` - Send a JSON response.
- `render($file)` - Render a view with the built-in mini template engine, you can also pass in your own data.
- `redirect($path)` - Redirect to another path - eg. `/example/login`
- `status($status)` - Set the status code (defaults to 200, optional)


> More methods will also be added in the future.

You can access these any functions outside your index or main file too by using the namespace `Trulyao\PhpRouter\HTTP`. Also, you are not constrained to any particular coding style or variable naming convention either.

## Views & Templates
In views, you are provided with the following variables by default:
- `query` -  The query string values.
- `body` - The request body values.
- `params` - The request params values.
- `headers` - The request headers.
- `data` - User-defined data eg. current user from JWT
> Note: headers are made case-insensitive while accessing them in views.

## Error Pages

You can easily add a custom 404 and 500 page by creating a file in the `/views` directory (or wherever your base path is; where your controllers or views are) called `404.php` and `500.php`

## Contribute

- Fork the repository on GitHub
- Add your own code
- Create a pull request with your changes highlighted
- For more information, contact me [here](https://twitter.com/trulyao)

> This documentation will be updated as soon as new features and methods are added to the package.
