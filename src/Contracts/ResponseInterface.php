<?php

namespace Twenga\Contracts;

use Psr\Http\Message\ResponseInterface as HttpResponse;

interface ResponseInterface{
    public function __construct(HttpResponse $response);
    public function success();
    public function result();
}
