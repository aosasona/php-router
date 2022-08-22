<?php

namespace Trulyao\PhpRouter;

use Closure;
use Exception;
use Trulyao\PhpRouter\HTTP\Middleware;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpRouter\HTTP\Response as Response;

class Router
{
    protected string $source_path;
    protected string $base_path;
    protected string $method;
    protected string $request_path;
    protected array $request_params;
    public array $routes;
    protected array $allowed_content_types;
    protected ?string $route_cache;
    private $content_type;

    public function __construct($source_path, $base_path = "")
    {
        $this->source_path = $source_path;
        $this->base_path = $base_path;
        $this->method = $_SERVER['REQUEST_METHOD'] ?? "GET";
        $this->strip_extra_url_data();
        $this->extract_params_from_request_path();
        $this->routes = [];
        $this->route_cache = null;
        $this->content_type = isset($_SERVER['HTTP_CONTENT_TYPE']) ? @explode(";", $_SERVER['HTTP_CONTENT_TYPE'])[0] ?? null : null;
        $this->allowed_content_types = [
            "application/json",
            "multipart/form-data",
            "text/html",
        ];
    }

    /**
     * @param array $content_types
     * @return void
     */
    public function allowed(array $content_types)
    {
        $this->allowed_content_types = $content_types;
    }


    /**
     * @param $file_name
     * @return string
     * @description Get file path from source path and request path
     */
    private function get_file_path($file_name): string
    {
        return $this->source_path . '/' . $file_name;
    }


    /**
     * @param $file_name
     * @return bool
     * @description Check if the controller/view file exists
     */
    private function check_file_exists($file_name): bool
    {
        return file_exists($this->get_file_path($file_name));
    }

    /**
     * @param int $error_code
     * @return void
     * @description Return error page
     */
    private function send_error_page(int $error_code = 404)
    {
        $this->handle_error($error_code);
    }

    /**
     * @param $path
     * @return bool
     * @description Compare current path with the route path
     */
    private function compare_current_path($path): bool
    {
        $path = $path !== "/" ? $path : "";
        $path = $this->base_path . $path;

        if (ltrim(rtrim($path, "/"), "/") === ltrim(rtrim($this->request_path, "/"), "/")) {
            return true;
        }

        $path_parts = explode("/", ltrim(rtrim($path, "/"), "/"));
        $request_parts = $this->request_params;

        foreach ($path_parts as $key => $path_value) {
            if (count($path_parts) === count($request_parts)) {
                if (strpos($path_value, ":") === 0) {
                    unset($path_parts[$key]);
                    unset($request_parts[$key]);
                }
                if (count(array_diff($path_parts, $request_parts)) === 0) {
                    return true;
                }
            }
        }

        return false;
    }


    /**
     * @param $data
     * @param string $method
     * @return void
     * @description Add a route to the routes array
     */
    private function add_route($data, string $method = "GET")
    {
        $path = $this->route_cache !== null ? $this->route_cache : $data[0];

        list($params, $path, $path_array, $dynamic) = $this->extract_route_details($path);

        $this->routes[] = [
            "method" => $method,
            "path" => $path,
            "cb" => end($data),
            "middleware" => count($data) > 1 ? array_slice($data, $this->route_cache !== null ? 0 : 1, -1) : [],
            "params" => $params,
            "path_array" => $path_array,
            "dynamic" => $dynamic
        ];

    }


    /**
     * @param string $path
     * @return $this
     */
    public function route(string $path): Router
    {
        $this->route_cache = $path;
        return $this;
    }


    /**
     * @param mixed | array | callable | Closure ...$data
     * @return self
     * @description Add a GET route to the routes array
     */
    public function get(...$data): Router
    {
        $this->add_route($data, "GET");
        return $this;
    }


    /**
     * @param mixed | array | callable | Closure ...$data
     * @return self
     * @description Add a POST route to the routes array
     */
    public function post(...$data): Router
    {
        $this->add_route($data, "POST");
        return $this;
    }


    /**
     * @param mixed ...$data
     * @return self
     * @description Add a DELETE route to the routes array
     */
    public function delete(...$data): Router
    {
        $this->add_route($data, "DELETE");
        return $this;
    }

    # Create a PUT route

    /**
     * @param mixed ...$data
     * @return self
     */
    public function put(...$data): Router
    {
        $this->add_route($data, "PUT");
        return $this;
    }


    /**
     * @param $_
     * @param $method
     * @return mixed|null
     */
    protected function get_route($_, $method)
    {
        foreach ($this->routes as $route) {
            if ($route["method"] === $method && $this->compare_current_path($route["path"])) {
                return $route;
            }
        }
        return null;
    }


    /**
     * @param $route
     * @return array
     */
    private function get_params_values($route): array
    {
        try {
            $params = $route["params"];
            $values = [];
            foreach ($params as $param) {
                $current_index = array_search(":{$param}", $route["path_array"]);
                $values[$param] = htmlspecialchars($this->request_params[$current_index]);
            }
            return $values;
        } catch (Exception $e) {
            return [];
        }
    }


    /**
     * @param $method
     * @return void
     */
    private function auto_serve($method): void
    {
        try {

            if ($this->content_type && !in_array($this->content_type, $this->allowed_content_types) && $this->method !== "GET") {
                $this->send_error_page(405);
                exit;
            }

            $route = $this->get_route($this->request_path, strtoupper($method));

            $params = count($route["params"] ?? []) > 0 ? $this->get_params_values($route) : [];
            $response = new Response($this->source_path);
            $request = new Request([$_GET, $_POST], $params, $this->request_path, $this->source_path);

            if ($route !== null) {
                if (count($route["middleware"]) > 0) {
                    $middleware = new Middleware($route["middleware"], $request, $response);
                    $middleware->handle();
                }
                $route['cb']($request, $response);
            } else {
                $this->send_error_page();
            }
            return;
        } catch (Exception $e) {
            $this->send_error_page(500);
            exit;
        }
    }


    # Serve routes and their controllers
    public function serve()
    {
        $this->auto_serve(strtoupper($_SERVER["REQUEST_METHOD"]));
    }

    /**
     * @param $error_code
     * @return void
     */
    public function handle_error($error_code): void
    {
        switch ($error_code) {
            case 404:
                header("HTTP/1.0 404 Not Found");
                if ($this->check_file_exists('404.php')) {
                    include $this->get_file_path('404.php');
                } else {
                    include __DIR__ . "/defaults/404.php";
                }
                break;
            case 405:
                header("HTTP/1.0 405 Method Not Allowed");
                if ($this->check_file_exists('405.php')) {
                    include $this->get_file_path('405.php');
                } else {
                    include __DIR__ . "/defaults/405.php";
                }
                break;
            default:
                header("HTTP/1.0 500 Internal Server Error");
                if ($this->check_file_exists('500.php')) {
                    include $this->get_file_path('500.php');
                } else {
                    include __DIR__ . "/defaults/500.php";
                }
                break;
        }
    }

    /**
     * @return void
     */
    private function extract_params_from_request_path(): void
    {
        $this->request_params = explode('/', ltrim(rtrim($this->request_path, "/"), "/"));
        $this->request_params = array_filter($this->request_params, function ($value) {
            return $value !== '';
        });
        $this->request_params = array_values($this->request_params);
    }

    /**
     * @param $path
     * @return array
     */
    protected function extract_route_details($path): array
    {
        $params = [];
        $path = empty($this->base_path) ? ltrim(rtrim($path, "/"), "/") : rtrim($path, "/");
        $path_array = explode('/', $path);

        foreach ($path_array as $key => $value) {
            if (!empty($value)) {
                if (strpos($value, ":") !== false && strpos($value, ":") === 0) {
                    $params[] = str_replace(":", "", $value);
                }
            }
        }
        $dynamic = count($params) > 0;
        return array($params, $path, $path_array, $dynamic);
    }

    /**
     * @return void
     */
    protected function strip_extra_url_data()
    {
        $this->request_path = $_SERVER['REQUEST_URI'] ?? "";
        $pos = strpos($this->request_path, "?");

        if (!$pos || strlen) {
            $this->request_path =  ltrim(rtrim($_SERVER['REQUEST_URI'] ?? "", "/"), "/");
            return;
        }
        
        $sub = substr($this->request_path, 0, $pos);
        $this->request_path = $sub;
        return;
    }
}
