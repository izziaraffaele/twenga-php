<?php
namespace Twenga;

use GuzzleHttp\ClientInterface;
use Psr\Http\Message\RequestInterface;

/**
 * Client interface for Twenga Report API.
 *
 * @link http://developer.affinitad.com/reportapi/report
 */
class Client
{
    const AUTH_ENDPOINT = '/authenticate';

    /**
     * @var ClientInterface
     */
    private $_httpClient;

     /**
     * Twenga auth username
     * @var string
     */
    private $username = '';

    /**
     * Twenga auth password
     * @var string
     */
    private $password = '';

    /**
     * Twenga auth token
     * @var string
     */
    private $token;

    /**
     * If you provide any parameters if will instantiate a HTTP client on construction.
     * Otherwise it will create one when required.
     *
     * @param string $auth Twenga auth credentials.
     * @param string $host Twenga API host. Defaults to 'https://api.affinitad.com'
     * @param string $version Zanox API version. Defaults to 2
     */
    public function __construct(array $auth = null, $host = null, $version = null)
    {
        // lazily instantiante
        if ($host || $version) 
        {
            $client = default_http_client($host, $version);
            $this->setHttpClient($client);
        }

        if($auth)
        {
            $this->setCredentials($auth['username'], $auth['password']);
        }
    }

    public function getHttpClient()
    {
        if (!$this->_httpClient) 
        {
            $this->_httpClient = default_http_client();
        }

        return $this->_httpClient;
    }

    public function setHttpClient(ClientInterface $httpClient)
    {
        $this->_httpClient = $httpClient;
        return $this;
    }

    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        return $this;
    }
    
    public function getAuthToken()
    {
        if(!$this->token && $this->username && $this->password)
        {
            $this->token = $this->authorize();
        }
        
        return $this->token;
    }

    public function send($method, $path, array $query = [])
    {
        $token = $this->getAuthToken();
        
        if(!$token)
        {
            throw new TwengaException('Invalid Api credentials');
        }

        $client = $this->getHttpClient();

        $query['token'] = $token;
        $httpResponse = $client->request($method, $path, ['query' => $query]);

        return new Response( $httpResponse );
    }

    protected function authorize()
    {
        if($this->username && $this->password)
        {
            $response = $this->getHttpClient()->request('GET', self::AUTH_ENDPOINT, [
                'auth' => [$this->username, $this->password]
            ]);

            if($response->getStatusCode() === 200)
            {
                $body = $response->getBody();
                return $body['result']['token'];
            }
        }
        
        return false;
    }
}
