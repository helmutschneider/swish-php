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
Swish documentation as of 2016-04-11: https://www.getswish.se/content/uploads/2015/06/Guide-Swish-API_160329.pdf

Begin by obtaining the SSL certificates required by Swish. The Swish server itself uses a self-signed root
certificated so a CA-bundle to verify its origin is needed. You will also need a client certificate and
corresponding private key so the Swish server can identify you.

```php
$rootCert = './swish-root.crt'; // forwarded to guzzle's "verify" option
$clientCert = './client-cert.crt'; // forwarded to guzzle's "cert" option
$clientCertKey = ['./key.pem', 'key-password']; // forwarded to guzzle's "ssl_key" option
$client = Client::make($rootCert, $clientCert, $clientCertKey);

$response = $client->createPaymentRequest([
    'callbackUrl' => 'https://localhost/swish',
    'payeePaymentReference' => '12345',
    'payerAlias' => '4671234768',
    'payeeAlias' => '1231181189',
    'amount' => '100',
    'currency' => 'SEK',
]);
$data = Util::decodeResponse($response);
```

## Notes
The bundled php & curl on OSX do not work well with the Swish api. This is probably because they were compiled with
SecureTransport and not OpenSSL.

## Run the tests
```shell
vendor/bin/codecept run
```
