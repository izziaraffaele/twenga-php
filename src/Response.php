<?php

namespace Twenga;

use Psr\Http\Message\ResponseInterface as HttpResponse;

class Response implements Contracts\ResponseInterface {

    protected $response;

    public function __construct(HttpResponse $response)
    {
        $this->response = $response;
    }

    public function success()
    {
        return $this->response->getStatusCode() === 200;
    }

    public function result()
    {
        $body = $this->response->getBody();

        if(is_array($body) && isset($body['result']))
        {
            return $body['result'];
        }
    }
}
