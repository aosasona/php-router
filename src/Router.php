<?php
namespace Trulyao\PhpRouter;

use \Trulyao\PhpRouter\Helper as response_helper;

class Router {
    public string $source_path;
    public string $base_path;
    public string $method;
    public string $request_path;
    public array $request_params;
    public array $routes;

    public function __construct($source_path, $base_path = "")
    {
        $this->source_path = $source_path;
        $this->base_path = $base_path;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->request_path = rtrim($_SERVER['REQUEST_URI'], "/");

        if ($pos = strpos($this->request_path, "?")) {
            $this->request_path = substr($this->request_path, 0, $pos);
        }

        $this->request_params = explode('/', rtrim($this->request_path, "/"));
        $this->request_params = array_filter($this->request_params, function ($value) {
            return $value !== '';
        });
        $this->request_params = array_values($this->request_params);
        $this->routes = [];
    }

    // Get file path from source path and request path
    private function get_file_path($file_name): string
    {
        return $this->source_path . '/' . $file_name;
    }

    // Check if the controller file exists
    private function check_file_exists($file_name): bool
    {
        return file_exists($this->get_file_path($file_name));
    }

    // Send error message to the client
    private function send_error_page($error_code = 404): void {
        switch($error_code) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                if($this->check_file_exists('404.php')) {
                    include $this->get_file_path('404.php');
                } else {
                    include __DIR__."/defaults/404.php";
                }
                break;
            case 405:
                header("HTTP/1.0 405 Method Not Allowed");
                break;
            default:
                header("HTTP/1.0 500 Internal Server Error");
                if($this->check_file_exists('500.php')) {
                    include $this->get_file_path('500.php');
                } else {
                    include __DIR__."/defaults/500.php";
                }
                break;
        }
    }

    private function compare_current_path($path): bool
    {
        $path = $path !== "/" ? $path : "";
        $path = "/".$this->base_path . $path;
        $path = rtrim($path, "/");
        return $this->request_path === $path;
    }

    protected function add_route($path, $cb, $method = "GET"): void
    {
        $params = [];
        $path_array = explode('/', $path);
        foreach($path_array as $key => $value) {
            if(strpos($value, ":") !== false) {
                $params[] = str_replace(":", "", $value);
            }
        }
        $dynamic = count($params) > 0;

        $this->routes[] = [
            "method" => $method,
            "path" => $path,
            "cb" => $cb,
            "params" => $params,
            "path_array" => $path_array,
            "dynamic" => $dynamic
        ];
    }

    // Create a GET route
    public function get($path, $cb): void {
        $this->add_route($path, $cb, "GET");
    }

    // Create a POST route
    public function post($path, $cb) {
        $this->add_route($path, $cb, "POST");
    }

    // Create a DELETE route
    public function delete($path, $cb) {
        $this->add_route($path, $cb, "DELETE");
    }

    // Create a PUT route
    public function put($path, $cb) {
        $this->add_route($path, $cb, "PUT");
    }

    // Get a route based on the request method and path
    private function get_route($path, $method) {
        foreach($this->routes as $route) {
            if($route["method"] === $method && $this->compare_current_path($route["path"])) {
                return $route;
            }
        }
        return null;
    }

    private function serve_route($path, $file) {
        try {
            if ($this->compare_current_path($path)) {
                if ($this->check_file_exists($file)) {
                    include $this->get_file_path($file);
                } else {
                    $this->send_error_page(404);
                }
            }
        } catch(\Exception $e) {
            $this->send_error_page(500);
        }
    }

    private function auto_serve($method){
        $response = new response_helper\Response($this->source_path);
        $request = new response_helper\Request([$_GET, $_POST]);
//        print_r($this->routes[count($this->routes) - 1]);
        $route = $this->get_route($this->request_path, strtoupper($method));
        if($route !== null) {
            !$route["dynamic"] ? $route['cb']($request, $response) : $route['cb']($this->request_params);
        } else {
            $this->send_error_page(404);
        }
        exit;
    }


    // Serve routes and their controllers
    public function serve() {
            switch($this->method) {
                case "GET":
                    $this->auto_serve("GET");
                    break;
                case "POST":
                    $this->auto_serve("POST");
                    break;
                case "DELETE":
                    $this->auto_serve("DELETE");
                    break;
                case "PUT":
                    $this->auto_serve("PUT");
                    break;
            }
    }
}