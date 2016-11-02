<?php
namespace Twenga;

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
    
    public function getAuthToken()
    {
        if(!$this->token)
        {
            if($this->username && $this->password)
            {
                $this->token = $this->authorize();
            }
        }
        
        return $this->token;
    }

    public function send($method, $path, array $query = [])
    {
        $token = $this->getAuthToken();
        
        if(!$token)
        {
            throw new Exceptions\TwengaException('Invalid Api credentials');
        }

        return $this->request($method, [
            CURLOPT_URL => $this->getPath($path, $query).'&token='.$token,
            CURLOPT_HTTPHEADER => [
                'cache-control: no-cache',
                'content-type: application/json'
            ]
        ]);
    }

    protected function authorize()
    {
        if($this->username && $this->password)
        { 
            $response = $this->request('GET', [
                CURLOPT_URL => $this->getPath(self::AUTH_ENDPOINT),
                CURLOPT_HTTPHEADER => [
                    'authorization: Basic '.base64_encode($this->username.':'.$this->password),
                    'cache-control: no-cache',
                    'content-type: application/json'
                ],
            ]);

            if($response->success())
            {
                return $response->result('token');
            }
        }
        
        return false;
    }

    protected function getPath($path, array $query = null)
    {
        $query = ($query) ? http_build_query($query) : '';
        return $this->host.$path.'?'.$query;
    }

    protected function request($method, array $options, $execution = 1)
    {
        $curl = curl_init();

        $defaultOptions = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $method
        ];

        curl_setopt_array($curl, $defaultOptions + $options);

        $response = curl_exec($curl);
        $code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if(in_array($code,[502, 504]) && $execution <= 3)
        {
            $execution += 1;
            return $this->request($method, $options, $execution);
        }

        return new Response($code, $response);
    }
}
