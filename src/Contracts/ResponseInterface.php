<?php

namespace Twenga\Contracts;

interface ResponseInterface{
    public function __construct($error, $response);
    public function success();
    public function result();
}
