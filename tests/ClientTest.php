<?php

namespace Twenga\Tests;

use Twenga\Client;
use Mockery\Mock;
use Mockery as m;

class ClientTest extends \PHPUnit_Framework_TestCase{
    const VALID_TOKEN = 'authToken';

    public function testGetHttpClient()
    {
        $client = new Client();
        $this->assertInstanceOf('GuzzleHttp\ClientInterface', $client->getHttpClient());
    }

    public function testGetAuthToken()
    {
        $client = new Client([
            'username' => 'username',
            'password' => 'password'
        ]);

        $client->setHttpClient($this->getMockedHttpClient());
        $token = $client->getAuthToken();

        $this->assertSame(self::VALID_TOKEN, $token);

        return $client;
    }

    public function testSend()
    {
        $requestOptions = [
          'query' => [
            'token' => self::VALID_TOKEN,
            'param' => 'value'
          ]
        ];

        $httpRequest = m::mock('GuzzleHttp\Psr7\Request');

        $httpClient = $this->getMockedHttpClient();
        $httpClient
            ->shouldReceive('request')
            ->times(1)
            ->with('GET', '/report', $requestOptions)
            ->andReturn(m::mock('GuzzleHttp\Psr7\Response'));

        $client = new Client([
            'username' => 'username',
            'password' => 'password'
        ]);

        $client->setHttpClient($httpClient);

        $response = $client->send('GET', '/report', [
            'param' => 'value'
        ]);

        $this->assertInstanceOf('Twenga\Response', $response);
    }

    public function getMockedHttpClient()
    {
        $httpResponse = m::mock('GuzzleHttp\Psr7\Response');
        $httpResponse
            ->shouldReceive('getStatusCode')
            ->times(1)
            ->andReturn(200);

        $httpResponse
            ->shouldReceive('getBody')
            ->times(1)
            ->andReturn([
                'result' => [
                    'token' => self::VALID_TOKEN
                ]
            ]);

        $httpClient = m::mock('GuzzleHttp\Client');
        $httpClient
            ->shouldReceive('request')
            ->times(1)
            ->with('GET', Client::AUTH_ENDPOINT, [
              'auth' => ['username', 'password']
            ])
            ->andReturn($httpResponse);

        return $httpClient;
    }
}
