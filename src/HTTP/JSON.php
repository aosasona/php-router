<?php

namespace Trulyao\PhpRouter\HTTP;

class JSON
{
    public static function send(array $data) {
        header("X-Powered-By: "); // Disable X-Powered-By header for security reasons
        header('Content-Type: application/json');
        echo json_encode($data);
    }
}