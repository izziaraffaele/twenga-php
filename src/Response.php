<?php

namespace Twenga;

use Psr\Http\Message\ResponseInterface as HttpResponse;

class Response implements Contracts\ResponseInterface {

    protected $response;
    protected $code;

    public function __construct($code, $response)
    {
        $this->code = $code;
        $this->response = json_decode($response, true);
    }

    public function success()
    {
        return $this->code === 200;
    }

    public function result($field = null)
    {
        if(is_array($this->response) && isset($this->response['result']))
        {
            return $field ? $this->response['result'][$field] : $this->response['result'];
        }
    }

    public function code()
    {
        return $this->code;
    }
}
