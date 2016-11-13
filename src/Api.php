<?php

namespace Twenga;

class Api {

    protected $client;

    protected $retryCode;

    protected $retryMax = 0;
    
    protected $execution = 0;

    public function __construct(array $auth = null, $host = null, $version = null)
    {
        $this->client = new Client($auth, $host, $version);
    }

    public function getClient()
    {
        return $this->client;
    }

    public function setClient( Client $client )
    {
        $this->client = $client;
        return $this;
    }

    public function retryOn($code, $times = 3)
    {
        $this->retryCode = (int) $code;
        $this->retryMax = (int) $times;
        $this->execution = 0;

        return $this;
    }

    public function report(array $query)
    {
        try
        {
            $response = $this->client->send('GET', '/report', $query);
            $this->execution = 0;

            return $response;
        }
        catch(\GuzzleHttp\Exception\RequestException $e)
        {
            $this->execution += 1;

            if($this->shouldRetry($e->getStatusCode()))
            {
                return $this->report($query);
            }

            throw $e;
        }
    }

    protected function shouldRetry($code)
    {
        return $this->retryCode === (int) $code && $this->retryMax > $this->execution;
    }
}
