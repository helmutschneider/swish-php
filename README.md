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
use HelmutSchneider\Swish\Client;
use HelmutSchneider\Swish\Util;

// Swish CA root cert
$rootCert = 'path/to/swish-root.crt'; // forwarded to guzzle's "verify" option

// .pem-bundle containing your client cert and it's corresponding private key. forwarded to guzzle's "cert" option
$clientCert = ['path/to/client-cert.pem', 'password'];

$client = Client::make($rootCert, $clientCert);

$response = $client->createPaymentRequest([
    'callbackUrl' => 'https://localhost/swish',
    'payeePaymentReference' => '12345',
    'payerAlias' => '4671234768',
    'payeeAlias' => '1231181189',
    'amount' => '100',
    'currency' => 'SEK',
]);

$data = Util::decodeResponse($response);
var_dump($data);

//  Array
//  (
//      [errorCode] =>
//      [errorMessage] =>
//      [id] => 3F0CC97D3E7E4308AB357C506BCB0402
//      [payeePaymentReference] => 12345
//      [paymentReference] =>
//      [callbackUrl] => https://localhost/swish
//      [payerAlias] => 4671234768
//      [payeeAlias] => 1231181189
//      [amount] => 100
//      [currency] => SEK
//      [message] =>
//      [status] => CREATED
//      [dateCreated] => 2016-04-10T23:45:27.538Z
//      [datePaid] =>
//  )

```

## Notes for OSX
The bundled php & curl on OSX do not work well with the Swish api. This is probably because they were compiled with
SecureTransport and not openssl. If you have homebrew & xcode installed, you can compile php & curl with openssl like so:
```shell
brew install curl --with-openssl
brew install php70 --with-homebrew-curl
```

## Run the tests
To run the tests you need certificates for the Swish test server. They are provided by Swish in a pkcs12-bundle
which can be extracted with the "extract.sh" script included in this repository. Place the generated certs in `tests/_data`.
```shell
vendor/bin/codecept run
```
