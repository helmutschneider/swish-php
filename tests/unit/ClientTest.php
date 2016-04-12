<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2016-01-17
 * Time: 20:16
 */

namespace HelmutSchneider\Swish\Tests;


use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Handler\StreamHandler;
use HelmutSchneider\Swish\Client;
use HelmutSchneider\Swish\Util;

class ClientTest extends Test
{

    /**
     * @var array
     */
    private $paymentRequest = [
        'callbackUrl' => 'https://localhost/swish',
        'payeePaymentReference' => '12345',
        'payerAlias' => '4671234768',
        'payeeAlias' => '1231181189',
        'amount' => '100',
        'currency' => 'SEK',
    ];

    /**
     * @param $handler
     * @return Client
     */
    public function makeClient($handler) {
        $rootCert = __DIR__ . '/../_data/ca.crt';
        $clientCert = [__DIR__ . '/../_data/cl.pem', 'swish'];
        return Client::make($rootCert, $clientCert, Client::SWISH_TEST_URL, $handler);
    }

    /**
     * @return array
     */
    public function handlerProvider()
    {
        return [
            [new CurlHandler(), function_exists('curl_exec')],
            [new StreamHandler(), true],
            [null, true], // let guzzle choose the handler
        ];
    }

    /**
     * Randomize a 10-digit phone number
     * @return string
     */
    public function randomSwedishPhoneNumber()
    {
        $nums = '';
        for ($i = 0; $i < 8; $i++) {
            $nums .= mt_rand(0, 9);
        }
        return '46' . $nums;
    }

    /**
     * @dataProvider handlerProvider
     * @param $handler
     * @param bool $shouldTestWithHandler
     */
    public function testCreateGetPaymentRequestWithHandler($handler, $shouldTestWithHandler)
    {
        if (!$shouldTestWithHandler) {
            $this->markTestSkipped('Skipping handler test with ' . get_class($handler));
        }
        $client = $this->makeClient($handler);
        $paymentRequest = $this->paymentRequest;
        $paymentRequest['payerAlias'] = $this->randomSwedishPhoneNumber();
        codecept_debug($paymentRequest['payerAlias']);
        $res = $client->createPaymentRequest($paymentRequest);

        codecept_debug($res->getStatusCode());
        codecept_debug($res->getHeaders());
        codecept_debug((string)$res->getBody());

        $this->assertEquals(201, $res->getStatusCode());

        $id = Util::getPaymentRequestIdFromResponse($res);
        $res = $client->getPaymentRequest($id);
        $body = Util::decodeResponse($res);

        codecept_debug($body);

        $this->assertEquals($id, $body['id']);
    }

}
