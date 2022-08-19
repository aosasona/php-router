<?php

namespace Trulyao\PhpRouter\HTTP;

use phpDocumentor\Reflection\Types\Parent_;
use Trulyao\PhpRouter\HTTP\Request as Request;
use Trulyao\PhpRouter\HTTP\Response as Response;

class Middleware
{

    protected Request $request;
    protected Response $response;
    protected $middleware_array;


    /**
     * @param mixed | array $middleware_array
     * @param Request $request
     * @param Response $response
     */
    public function __construct($middleware_array, Request $request, Response $response)
    {
        $this->middleware_array = $middleware_array;
        $this->request = $request;
        $this->response = $response;
    }


    public function handle()
    {
        $middleware_array = $this->middleware_array;
        $request = $this->request;
        $response = $this->response;
        foreach ($middleware_array as $middleware) {
            $middleware($request, $response);

            if ($response->is_sent) {
                exit;
            }
        }
    }
}