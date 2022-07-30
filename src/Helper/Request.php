<?php

namespace Trulyao\PhpRouter\Helper;

class Request {

    public array $request_data;
    public array $request_params;

    public function __construct($request_data, $params = []) {
        $this->request_data = $request_data;
        $this->request_params = $params;
    }

    public function query($key = null) {
        [$get, $post] = $this->request_data;

        $get = array_map(function($value) {
            return htmlspecialchars($value);
        }, $get);

        return array_key_exists($key, $get) ? $get[$key] :($key !== null ? null : (count($get) > 0 ? $get : null));
    }

    public function body($key = null) {
        [$get, $post] = $this->request_data;

        $post = array_map(function($value) {
            return htmlspecialchars($value);
        }, $post);

        return array_key_exists($key, $post) ? $post[$key] :($key !== null ? null : (count($post) > 0 ? $post : null));
    }

    public function params($key = null) {
        return $this->request_params[$key] ?? ($key !== null ? null : $this->request_params);
    }
}