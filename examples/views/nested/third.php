<?php

use \Trulyao\PhpRouter\Helper as helper;

$sample_json = [
    "name" => "John Doe",
    "age" => "30",
    "address" => "123 Main St",
    "city" => "New York",
    "state" => "NY",
    "zip" => "10001"
];

helper\JSON::send($sample_json);