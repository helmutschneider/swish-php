# Swish-PHP
Swish-PHP is a small wrapper for the swish merchant api. See https://www.getswish.se/handel/ for more information.

## Dependencies
- php 5.5.9 or newer with curl & openssl
- composer

## Installation
```shell
git clone https://github.com/helmutschneider/swish-php.git
composer install
```

## Usage
Swish documentation as of 2016-01-23: https://www.getswish.se/content/uploads/2015/06/Guide-Certifikatsadministration_160118.pdf

Begin by obtaining the SSL certificates required by Swish. The Swish server itself uses a self-signed root
certificated so a CA-bundle to verify its origin is recommended. You will also need a client certificate and
corresponding private key so the Swish server can identify you.

```php
$guzzle = new \GuzzleHttp\Client();

// this is the production url - change if you want to use the test server
$baseUrl = 'https://swicpc.bankgirot.se/swish-cpcapi/api/v1';

// these options are forwarded to guzzle and may differ depending on your OS. Consult the guzzle documentation for
// more infomation: http://docs.guzzlephp.org/en/latest/request-options.html
$options = [
    'verify' => './swish-root.crt',
    'cert' => './client-cert.crt',
    'ssl_key' => ['./key.pem', 'key-password'],
];
$client = new \HelmutSchneider\Swish\Client($guzzle, $baseUrl, $options);
```

## Notes
The bundled php & curl on OSX do not work well with the Swish api. This is probably because they were compiled with
SecureTransport and not OpenSSL.

## Run the tests
```shell
vendor/bin/codecept run
```
