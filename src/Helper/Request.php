<?php

namespace Trulyao\PhpRouter\Helper;

class Request {

    public array $request_data;

    public function __construct($request_data) {
        $this->request_data = $request_data;
    }

    public function query($key) {
        [$get, $post] = $this->request_data;
        return array_key_exists($key, $get) ? $get[$key] :($key !== null ? null : (count($get) > 0 ? $get : null));
    }

    public function body($key) {
        [$get, $post] = $this->request_data;
        return array_key_exists($key, $post) ? $post[$key] :($key !== null ? null : (count($post) > 0 ? $post : null));
    }

    public function all(): array
    {
        [$get, $post] = $this->request_data;
        return array_merge($get, $post);
    }
}