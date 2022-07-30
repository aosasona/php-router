<?php

namespace Trulyao\PhpRouter\Helper;

class Response {

    public $source_path;

    public function __construct($source_path = "") {
        $this->source_path = $source_path;
        header_remove("X-Powered-By");
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
    public function error($message, $status): void {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode(["error" => $message]);
        exit;
    }

    public function send($content): Response
    {
        $content_type =  is_array($content) ? "application/json" : "text/html";
        header('Content-Type: '.$content_type);
        echo is_array($content) ? json_encode($content) : $content;
        return $this;
    }

    public function json($data): Response {
        header('Content-Type: application/json');
        echo json_encode($data);
        return $this;
    }

    public function redirect($url): Response {
        header('Location: '.$url);
        return $this;
    }

    public function use($file): Response
    {
        if ($this->check_file_exists($file)) {
            include $this->get_file_path($file);
        }
        return $this;
    }

    // Experimental
    public function send_file($file_path) {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
    }

    public function status ($status_code = 200): Response
    {
        http_response_code($status_code);
        return $this;
    }

}
