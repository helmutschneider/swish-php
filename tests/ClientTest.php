<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2016-01-17
 * Time: 20:16
 */

namespace HelmutSchneider\Swish\Tests;

use HelmutSchneider\Swish\Client;
use HelmutSchneider\Swish\PaymentRequest;
use HelmutSchneider\Swish\Refund;
use HelmutSchneider\Swish\ValidationException;

class ClientTest extends TestCase
{

    /**
     * @var Client
     */
    private $client;

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

    public function setUp()
    {
        parent::setUp();

        $rootCert = __DIR__ . '/_data/root.pem';
        $clientCert = [__DIR__ . '/_data/client.pem', ''];
        $this->client = Client::make($rootCert, $clientCert, Client::SWISH_TEST_URL);
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

    public function testCreateGetPaymentRequest()
    {
        $paymentRequest = new PaymentRequest($this->paymentRequest);
        $paymentRequest->payerAlias = $this->randomSwedishPhoneNumber();
        $id = $this->client->createPaymentRequest($paymentRequest);
        $res = $this->client->getPaymentRequest($id);
        $this->assertEquals($id, $res->id);
    }

    public function testThrowsValidationException()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('BE18: Payer alias is invalid');

        $paymentRequest = new PaymentRequest($this->paymentRequest);
        $paymentRequest->payerAlias = '123';

        $this->client->createPaymentRequest($paymentRequest);
    }

    public function testCreateRefund()
    {
        $pr = new PaymentRequest([
            'callbackUrl' => 'https://localhost/swish',
            'payeePaymentReference' => '12345',
            'payerAlias' => '4671234768',
            'payeeAlias' => '1231181189',
            'amount' => '100',
        ]);

        $id = $this->client->createPaymentRequest($pr);

        // the test server automatically sets the request
        // to "PAID" if we wait a couple of seconds.
        sleep(5);

        $res = $this->client->getPaymentRequest($id);

        $id = $this->client->createRefund(new Refund([
            'originalPaymentReference' => $res->paymentReference,
            'payerAlias' => '1231181189',
            'callbackUrl' => 'https://localhost/swish',
            'amount' => '100',
        ]));

        $refund = $this->client->getRefund($id);

        $this->assertInstanceOf(Refund::class, $refund);
    }

}
