<?php

namespace Trulyao\PhpRouter\Helper;

class HTML
{
    public static function send($html)
    {
        header("X-Powered-By: "); // Disable X-Powered-By header for security reasons
        header('Content-Type: text/html');
        echo $html;
    }
}