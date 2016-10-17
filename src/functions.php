<?php

namespace Twenga;

use GuzzleHttp\Client as GuzzleClient;

const DEFAULT_HOST      = 'https://api.affinitad.com';
const DEFAULT_VERSION   = 2;

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
    $config = default_http_config($host, $version);
    return new GuzzleClient($config);
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
    $base_uri = ($host ? trim($host, '/') : DEFAULT_HOST) . '/';

    return [
        'base_uri' => $base_uri,
        'headers' => [
            'Content-Type'  => 'application/json'
        ],
        'http_errors' => false,
    ];
}
