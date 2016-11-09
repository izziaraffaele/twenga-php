<?php
namespace Twenga;

use GuzzleHttp\Client as HttpClient;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;

/**
 * Client interface for Twenga Report API.
 *
 * @link http://developer.affinitad.com/reportapi/report
 */
class Client
{
    const AUTH_ENDPOINT = '/authenticate';
    const DEFAULT_HOST      = 'https://api.affinitad.com';
    const DEFAULT_VERSION   = 2;

    /**
     * @var string
     */
    private $host;

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
        $this->host = $host ?: self::DEFAULT_HOST;

        if($auth)
        {
            $this->setCredentials($auth['username'], $auth['password']);
        }
    }

    public function setCredentials($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
        return $this;
    }

    public function setHttpClient(HttpClient $client)
    {
        $this->client = $client;
    }
    
    public function getAuthToken()
    {
        return $this->token;
    }

    public function refreshAuthToken()
    {
        $response = $this->request('GET', self::AUTH_ENDPOINT, [
            'auth' => [$this->username, $this->password]
        ]);

        if($response->getStatusCode() === 200)
        {
            $data = $this->decodeBody($response->getBody());
            $this->token = $data['token'];

            return true;
        }
    }

    public function send($method, $path, array $query = [])
    {
        if(!$this->authorize())
        {
            throw new Exceptions\TwengaException('Invalid Api credentials');
        }

        $httpResponse = $this->request($method, $path, [
            'query' => array_merge($query, ['token' => $this->token]),
        ]);

        return new Response($httpResponse->getStatusCode(), json_decode($httpResponse->getBody(), true));
    }

    protected function request($method, $path, array $options)
    {
        return $this->client()->request($method, $path, $options);
    }

    protected function authorize()
    {
        if($this->token)
        {
            return true;
        }

        if($this->password && $this->username)
        {
            return $this->refreshAuthToken();
        }

        return false;
    }


    protected function client()
    {
        if(!$this->client)
        {
            $this->client = $this->createDefaultClient();
        }

        return $this->client;
    }

    protected function createDefaultClient()
    {
        return new HttpClient([
            'handler' => HandlerStack::create(new CurlHandler()),
            'query' => [
                'format' => 'json'
            ],
            'curl' => [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            ]
        ]);
    }
}
