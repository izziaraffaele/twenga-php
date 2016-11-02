<?php

namespace Twenga;

/**
 * Creates a pre-configured Guzzle Client with the default settings.
 *
 * @param string $host    Twenga API host. Defaults to 'https://api.affinitad.com'
 * @param string $version Twenga API version. Defaults to 2
 *
 * @return \GuzzleHttp\Client
 */
function default_http_client($host = null, $version = null)
{
    $curl = curl_init();
    $config = default_http_config($host, $version);

    curl_setopt_array($curl, $config);
    return $curl;
}

/**
 * Form default configuration settings for Guzzle Client.
 *
 * @param string $host    Twenga API host. Defaults to 'https://api.affinitad.com'
 * @param string $version Twenga API version. Defaults to 2
 *
 * @return array
 */
function default_http_config($host = null, $version = null)
{
    return [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 30,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    ];
}
