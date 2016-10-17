<?php

namespace Twenga;

class Api {

    protected $client;

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

    public function report(array $query)
    {
        return $this->client->send('GET', '/report', $query);
    }
}
