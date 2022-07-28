<?php
namespace Trulyao\PhpRouter;

class Router {
    public string $source_path;
    public string $method;
    public string $request_path;

    public function __construct($source_path) {
        $this->source_path = $source_path;
        $this->method = $_SERVER['REQUEST_METHOD'];
        $this->request_path = $_SERVER['REQUEST_URI'];
    }

    private function get_file_path($file_name): string
    {
        return $this->source_path . '/' . $file_name;
    }

    private function check_file_exists($file_name): bool
    {
        return file_exists($this->get_file_path($file_name));
    }

    private function send_error_page($error_code) {
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

    public function get($path, $file) {
        if ($this->method == 'GET' && $this->check_file_exists($file)) {
            header("HTTP/1.0 200 OK");
            include $this->get_file_path($file);
        } else {
            $this->send_error_page(404);
        }
    }

    public function post($path, $file) {
        if($this->method == 'POST') {
            include $file;
            exit;
        }
    }
}