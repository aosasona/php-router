<?php
namespace Trulyao\PhpRouter;

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

    // Create a GET route
    public function get($path, $file) {

        $this->routes[] = [
            "method" => "GET",
            "path" => $path,
            "file" => $file
        ];
    }

    // Create a POST route
    public function post($path, $file) {
        $this->routes[] = [
            "method" => "POST",
            "path" => $path,
            "file" => $file
        ];
    }

    public function delete($path, $file) {
        $this->routes[] = [
            "method" => "DELETE",
            "path" => $path,
            "file" => $file
        ];
    }

    public function put($path, $file) {
        $this->routes[] = [
            "method" => "PUT",
            "path" => $path,
            "file" => $file
        ];
    }

    private function get_route($path, $method) {
        foreach($this->routes as $route) {
            if($route["method"] === $method && $this->compare_current_path($route["path"])) {
                return $route;
            }
        }
        return null;
    }

    // TODO: clean these up a bit more
    private function serve_get($path, $file) {
        if($this->compare_current_path($path)) {
            if($this->check_file_exists($file)) {
                include $this->get_file_path($file);
            } else {
                $this->send_error_page(404);
            }
        }
    }

    private function serve_post($path, $file)
    {
        if($this->compare_current_path($path)) {
            if($this->check_file_exists($file)) {
                include $this->get_file_path($file);
            } else {
                $this->send_error_page(404);
            }
        }
    }

    private function serve_delete($path, $file)
    {
        if($this->compare_current_path($path)) {
            if($this->check_file_exists($file)) {
                include $this->get_file_path($file);
            } else {
                $this->send_error_page(404);
            }
        }
    }

    private function serve_put($path, $file)
    {
        if($this->compare_current_path($path)) {
            if($this->check_file_exists($file)) {
                include $this->get_file_path($file);
            } else {
                $this->send_error_page(404);
            }
        }
    }


    // Serve routes and their controllers
    public function run() {
            switch($this->method) {
                case "GET":
                    $route = $this->get_route($this->request_path, "GET");
                    if($route !== null) {
                        $this->serve_get($route['path'], $route['file']);
                    } else {
                        $this->send_error_page(404);
                    }
                    break;
                case "POST":
                    $route = $this->get_route($this->request_path, "POST");
                    $this->serve_post($route['path'], $route['file']);
                    break;
                case "DELETE":
                    $route = $this->get_route($this->request_path, "DELETE");
                    $this->serve_delete($route['path'], $route['file']);
                    break;
                case "PUT":
                    $route = $this->get_route($this->request_path, "PUT");
                    $this->serve_put($route['path'], $route['file']);
                    break;
            }
    }
}