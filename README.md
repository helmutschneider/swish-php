# Swish-PHP
Swish-PHP is a small wrapper for the swish merchant api. See https://www.getswish.se/handel/ for more information.

## Dependencies
- php 7.2.5 or newer with curl & openssl
- composer

## Upgrade notes

### 3.0.0

- The function `createPaymentRequest` now returns an object with the two properties `id` and `paymentRequestToken`. In earlier versions the id was returned directly.

## Installation via git
```shell
git clone https://github.com/helmutschneider/swish-php.git
composer install
```

## Installation via composer
```shell
composer require helmutschneider/swish-php:^3.0
```

## Obtaining test certificates
As of 2022-05-29, a test certificate bundle (mss\_test\_1.9.2.zip) can be found by visiting:
https://developer.swish.nu/documentation/environments#certificates

Begin by obtaining the SSL certificates required by Swish. The Swish server itself uses a self-signed root
certificated so a CA-bundle to verify its origin is needed. You will also need a client certificate and
corresponding private key so the Swish server can identify you.

As of 2022-05-29 the test certificates are called `Swish_Merchant_TestCertificate_1234679304.key`, `Swish Merchant Test Certificate 1231181189.pem` and `Swish_TLS_RootCA.pem`.
**You must concatenate `Swish_Merchant_TestCertificate_1234679304.key` and `Swish Merchant Test Certificate 1231181189.pem` together, otherwise they will not work with cURL.
This bundle is your client certificate.**

## Usage
The client closely mirrors the swish api:
```php
class Client
{

    /**
     * @param PaymentRequest $request
     * @return CreatePaymentRequestResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException
     */
    public function createPaymentRequest(PaymentRequest $request): CreatePaymentRequestResponse;
    
    /**
     * @param string $id Payment request id
     * @return PaymentRequest
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPaymentRequest(string $id): PaymentRequest;
    
    /**
     * @param Refund $refund
     * @return string refund id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException
     */
    public function createRefund(Refund $refund): string;
    
    /**
     * @param string $id Refund id
     * @return Refund
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRefund(string $id): Refund;
}
```
When you have the SSL certificates ready you may instantiate the client:
```php
use HelmutSchneider\Swish\Client;
use HelmutSchneider\Swish\PaymentRequest;

// Swish CA root cert
$rootCert = 'path/to/swish-root.crt'; // forwarded to guzzle's "verify" option

// .pem-bundle containing your client cert and it's corresponding private key. forwarded to guzzle's "cert" option
// you may use an empty string for "password" if you are using the test certificates.
$clientCert = ['path/to/client-cert.pem', 'password'];

$client = Client::make($rootCert, $clientCert);

$pr = new PaymentRequest([
    'callbackUrl' => 'https://localhost/swish',
    'payeePaymentReference' => '12345',
    'payerAlias' => '4671234768',
    'payeeAlias' => '1231181189',
    'amount' => '100',
])

$res = $client->createPaymentRequest($pr);

var_dump($res->id);

//
//  string(32) "0D3AD8F1AE484A57B82A87FAB8C602EB"
//

```

## Notes for OSX
The bundled PHP in OSX 10.12 and earlier is not compatible with the above approach of forwarding SSL certificates. You
must obtain a PHP-version that is compiled with cURL linked against OpenSSL or similar.

## Run the tests
To run the tests you need certificates for the Swish test server. Place the root certificate in `tests/_data/root.pem` and
the client certificate in `tests/_data/client.pem`.
```shell
vendor/bin/phpunit
```
