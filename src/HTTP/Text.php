<?php

namespace Trulyao\PhpRouter\HTTP;

class Text
{
    public static function send($text)
    {
        header("X-Powered-By: "); // Disable X-Powered-By header for security reasons
        header('Content-Type: text/plain');
        echo $text;
    }
}