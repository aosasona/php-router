<?php

namespace Trulyao\PhpRouter\HTTP;

use Trulyao\PhpRouter\Engines\TemplateEngine;

class Response
{

    public string $source_path;
    public int $status_code;
    public bool $is_sent;
    public bool $use_template_engine;

    public function __construct($source_path = "")
    {
        $this->source_path = $source_path;
        $this->status_code = 200;
        $this->use_template_engine = false;
        $this->is_sent = false;
        header_remove("X-Powered-By");
    }

    /**
     * @param $file_name
     * @return string
     */
    private function get_file_path($file_name): string
    {
        return $this->source_path . '/' . $file_name;
    }


    /**
     * @param $file_name
     * @return bool
     */
    private function check_file_exists($file_name): bool
    {
        return file_exists($this->get_file_path($file_name));
    }


    /**
     * @param $message
     * @param $status
     * @return $this
     */
    public function error($message, $status): Response
    {
        header('Content-Type: application/json');
        http_response_code($status);
        echo json_encode(["code" => $status, "error" => $message]);
        $this->is_sent = true;
        return $this;
    }

    /**
     * @param $content
     * @return $this
     */
    public function send($content): Response
    {
        $content_type = is_array($content) ? "application/json" : "text/html";
        header('Content-Type: ' . $content_type);
        header("X-Content-Type-Options: nosniff");
        http_response_code($this->status_code);
        echo is_array($content) ? json_encode($content) : $content;
        $this->is_sent = true;
        return $this;
    }

    /**
     * @param $data
     * @return $this
     */
    public function json($data): Response
    {
        header('Content-Type: application/json');
        http_response_code($this->status_code);
        echo json_encode($data);
        $this->is_sent = true;
        return $this;
    }

    /**
     * @param $url
     * @return $this
     */
    public function redirect($url): Response
    {
        header('HTTP/1.1 301 Moved Permanently');
        header('Location: ' . $url);
        return $this;
    }

    public function use_engine(): Response
    {
        $this->use_template_engine = true;
        return $this;
    }

    /**
     * @param string $file
     * @param Request|null $request
     * @param array|null $extra_data
     * @return self
     */
    public function render(string $file, ?Request $request = null,?array $extra_data = []): Response
    {
        if ($this->check_file_exists($file)) {
            $request_data = isset($request) ? $request->get_full_request_data() : null;
            $root_dir = isset($request) ? $request->get_root_dir() : __DIR__;
            $data = array_merge($request_data, $extra_data, ["root_dir" => $root_dir]);
            TemplateEngine::render($this->get_file_path($file), $data, $this->use_template_engine);
        } else {
            $this->send_error_page();
        }
        $this->is_sent = true;
        return $this;
    }

    /**
     * @param int $status_code
     * @return $this
     */
    public function status(int $status_code = 200): Response
    {
        http_response_code($status_code ?? 200);
        $this->status_code = $status_code;
        return $this;
    }

    // Experimental

    /**
     * @param $file_path
     * @return $this
     */
    public function send_file($file_path): Response
    {
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($file_path) . '"');
        header('Content-Length: ' . filesize($file_path));
        readfile($file_path);
        $this->is_sent = true;
        return $this;
    }

    private function send_error_page()
    {
        $this->status(404);
        $this->render("404.php");
    }
}
