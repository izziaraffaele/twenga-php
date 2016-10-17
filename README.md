Twenga PHP API Client
======

A very user-friendly PHP client for [Twenga](http://developer.affinitad.com/reportapi/report).

Requirements:
- PHP must be 5.5 or higher.
- [Guzzle 6](https://github.com/guzzle/guzzle) as HTTP client.


## Instalation

Use [Composer](http://getcomposer.org).

Install Composer Globally (Linux / Unix / OSX):

```bash
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
```

Run this Composer command to install the latest stable version of the client, in the current folder:

```bash
composer require izziaraffaele/twenga-php
```

After installing, require Composer's autoloader and you're good to go:

```php
<?php
require 'vendor/autoload.php';
```


## Getting Started

```php
use Twenga\Api;

// Initialize the API
$api = new Api([
    'username' => 'username',
    'password' => 'password'
]);

$response = $api->report([
    'start' => 'YYYY-MM-DD',
    'end' => 'YYYY-MM-DD'
]);

if($response->success())
{
    var_dump($response->result());
}

```

## Docs

Please refer to the source code for now, while a proper documentation is made.
