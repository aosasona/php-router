<?php

namespace Trulyao\PhpRouter\HTTP;

class Request
{

    private ?array $request_data;
    private ?array $request_params;
    public string $request_path;
    private string $content_type;

    public function __construct($request_data, $params = [], $path = "")
    {
        $this->request_data = $request_data;
        $this->request_params = $params;
        $this->request_path = $path;
        $this->content_type = $_SERVER['HTTP_CONTENT_TYPE'] ?? "text/html";
    }

    /**
     * @param $key
     * @return array|mixed|string|string[]|null
     */
    public function query($key = null)
    {
        @[$get, $post] = $this->request_data;

        $get = array_map(function ($value) {
            return htmlspecialchars_decode($value);
        }, $get);

        return array_key_exists($key, $get) ? $get[$key] : ($key !== null ? null : (count($get) > 0 ? $get : []));
    }

    /**
     * @param $key
     * @return array|mixed|string|string[]|null
     */
    public function body($key = null)
    {

        if ($this->content_type === "application/json") {
            $body = json_decode(file_get_contents("php://input"), true);
        } else {
            @[$get, $post] = $this->request_data;
            $post = array_map(function ($value) {
                return htmlspecialchars($value);
            }, $post);
            $body = $post;
        }

        return array_key_exists($key, $body) ? $body[$key] : ($key !== null ? null : (count($body) > 0 ? $body : []));
    }

    /**
     * @param $key
     * @return array|mixed|null
     */
    public function params($key = null)
    {
        return array_key_exists($key, $this->request_params)
            ? $this->request_params[$key]
            : ($key !== null ? null : (count($this->request_params) > 0 ? $this->request_params : []));
    }

    /**
     * @return mixed|string
     */
    public function path()
    {
        return $this->request_path;
    }

    /**
     * @return array
     */
    public function get_headers(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }


    /**
     * @return array
     */
    protected function get_request_data(): array {
        return [
            "query" => $this->query(),
            "body" => $this->body(),
            "params" => $this->request_params
        ];
    }
}
