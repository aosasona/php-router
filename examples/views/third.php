<?php

use \Trulyao\PhpRouter\Helpers as helpers;

$sample_json = [
    "name" => "John Doe",
    "age" => "30",
    "address" => "123 Main St",
    "city" => "New York",
    "state" => "NY",
    "zip" => "10001"
];

header("Content-Type: application/json");
echo json_encode($sample_json);