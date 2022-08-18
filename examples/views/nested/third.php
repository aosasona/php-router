<?php

use \Trulyao\PhpRouter\HTTP as helper;

$sample_json = [
    "name" => "John Doe",
    "age" => "30",
    "address" => "123 Main St",
    "city" => "New York",
    "state" => "NY",
    "zip" => "10001"
];

Utils\JSON::send($sample_json);