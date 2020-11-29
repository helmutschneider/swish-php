<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2016-01-17
 * Time: 20:06
 */

namespace HelmutSchneider\Swish;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use HelmutSchneider\Swish\Payout\PayoutRequest;
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 *
 * @package HelmutSchneider\Swish
 */
class Client
{
    const SWISH_PRODUCTION_URL = 'https://cpc.getswish.net/swish-cpcapi/api/';
    const SWISH_TEST_URL = 'https://mss.cpc.getswish.net/swish-cpcapi/api/';
    const SWISH_QR_URL = 'https://mpc.getswish.net/qrg-swish/api/';
    const CONTENT_TYPE_JSON = 'application/json';
    /**
     * The serial number for the client certificate
     *
     * @var string
     */
    public static $certificateSerialNumber = '';
    /**
     * The private key of the client certificate bundle.
     *
     * @var resource
     */
    public static $certificatePrivateKey;

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * Client constructor.
     *
     * @param ClientInterface $client
     * @param string $baseUrl
     */
    function __construct(ClientInterface $client, $baseUrl)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @param string $method HTTP-method
     * @param string $endpoint
     * @param array $options guzzle options
     * @return ResponseInterface
     * @throws GuzzleException
     * @throws ValidationException|CertificateException
     */
    protected function sendRequest($method, $endpoint, array $options = [])
    {
        try {
            return $this->client->request($method, $endpoint, array_merge([
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE_JSON,
                    'Accept' => self::CONTENT_TYPE_JSON,
                ],
            ], $options));
        } catch (ClientException $e) {
            switch ($e->getResponse()->getStatusCode()) {
                case 400:
                    throw new CertificateException($e->getResponse());
                case 403:
                case 422:
                    throw new ValidationException($e->getResponse());
            }
            throw $e;
        }
    }

    /**
     * @param string[] $body
     * @return string[]
     */
    protected function filterRequestBody(array $body)
    {
        $filtered = $body;
        foreach ($filtered as $key => $value) {
            if (empty($filtered[$key])) {
                unset($filtered[$key]);
            }
        }
        return $filtered;
    }

    /**
     * @param PaymentRequest $request
     * @return array payment request id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException|CertificateException
     */
    public function createPaymentRequest(PaymentRequest $request)
    {
        $response = $this->sendRequest('POST', 'v1/paymentrequests', [
            'json' => $this->filterRequestBody((array)$request),
        ]);

        return Util::getPaymentRequestIdsFromResponse($response);
    }

    /**
     * Cancel a given Payment Request by its id
     * @param string $id Payment request id to cancel
     * @return PaymentRequest
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException|CertificateException
     */
    public function cancelPaymentRequest($id)
    {
        $response = $this->sendRequest('PATCH', 'v1/paymentrequests/' . $id, [
            'json' => [
                ['op' => 'replace', 'path' => '/status', 'value' => 'cancelled']
            ]
        ]);

        return json_decode((string)$response->getBody(), true);
    }

    /**
     * @param string $id Payment request id
     * @return PaymentRequest
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException|CertificateException
     */
    public function getPaymentRequest($id)
    {
        $response = $this->sendRequest('GET', 'v1/paymentrequests/' . $id);

        return new PaymentRequest(
            json_decode((string)$response->getBody(), true)
        );
    }

    /**
     * @param PaymentRequest $paymentRequest
     * @param string $instructionUUID - 32 chars uppercase in format ^[0-9A-F]{32}$. Leave blank to let PHP automatically generate a instruction UUID
     * @return array
     * @throws CertificateException
     * @throws GuzzleException
     * @throws ValidationException
     */
    public function createPaymentRequestV2(PaymentRequest $paymentRequest, $instructionUUID = '')
    {
        if($instructionUUID === '') {
            $instructionUUID = $this->generateUUID();
        }

        $response = $this->sendRequest('PUT', 'v2/paymentrequests/'.$instructionUUID, [
            'json' => $this->filterRequestBody((array)$paymentRequest),
        ]);

        return Util::getPaymentRequestIdsFromResponse($response);
    }

    /**
     * @param Refund $refund
     * @return string refund id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException|CertificateException
     */
    public function createRefund(Refund $refund)
    {
        $response = $this->sendRequest('POST', 'v1/refunds', [
            'json' => $this->filterRequestBody((array)$refund),
        ]);

        return Util::getObjectIdFromResponse($response);
    }

    /**
     * @param Refund $refund
     * @param string $instructionUUID - 32 chars uppercase in format ^[0-9A-F]{32}$. Leave blank to let PHP automatically generate a instruction UUID
     * @return string
     * @throws CertificateException
     * @throws GuzzleException
     * @throws ValidationException
     */
    public function createRefundV2(Refund $refund, $instructionUUID = '')
    {
        if($instructionUUID === '') {
            $instructionUUID = $this->generateUUID();
        }

        $response = $this->sendRequest('PUT', 'v2/refunds/'.$instructionUUID, [
            'json' => $this->filterRequestBody((array)$refund),
        ]);

        return Util::getObjectIdFromResponse($response);
    }

    /**
     * @param string $id Refund id
     * @return Refund
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException|CertificateException
     */
    public function getRefund($id)
    {
        $response = $this->sendRequest('GET', 'v1/refunds/' . $id);

        return new Refund(
            json_decode((string)$response->getBody(), true)
        );
    }

    /**
     * Create a Swish payout request.
     *
     * @param PayoutRequest $payoutRequest
     * @return string
     * @throws CertificateException
     * @throws GuzzleException
     * @throws ValidationException
     */
    public function createPayoutRequest(PayoutRequest $payoutRequest)
    {
        $response = $this->sendRequest('POST', 'v1/payouts', [
            'json' => $this->filterRequestBody((array)$payoutRequest),
        ]);

        return Util::getObjectIdFromResponse($response);
    }

    /**
     * Get the current status from a given payout request.
     *
     * @param $payoutInstructionUUID - The initially set Payout Instruction UUID to query.
     * @return PayoutRequest
     * @throws CertificateException
     * @throws GuzzleException
     * @throws ValidationException
     */
    public function getPayoutRequest($payoutInstructionUUID)
    {
        $response = $this->sendRequest('GET', 'v1/payouts/' . $payoutInstructionUUID);

        return new PayoutRequest(
            json_decode((string)$response->getBody(), true)
        );
    }

    /**
     * Generate a QR Code for a given payment request token.
     * @param QRCodeRequest $QRCodeRequest
     * @return \Psr\Http\Message\StreamInterface
     * @throws CertificateException
     * @throws GuzzleException
     * @throws ValidationException
     */
    public function createQRCode(QRCodeRequest $QRCodeRequest)
    {
        $response = $this->sendRequest('POST', 'v1/commerce', [
            'json' => $this->filterRequestBody((array)$QRCodeRequest),
        ], static::SWISH_QR_URL);
        return $response->getBody();
    }

    /**
     * @return string - A time based UUID
     */
    private function generateUUID()
    {
        return strtoupper(md5(time()));
    }

    /**
     * @param string $rootCert path to the swish CA root cert chain. forwarded to guzzle's "verify" option.
     * @param string|string[] $clientCert path to a .pem-bundle containing the client side cert
     *                                    and it's corresponding private key. If the private key is
     *                                    password protected, pass an array ['PATH', 'PASSWORD'].
     *                                    forwarded to guzzle's "cert" option.
     * @param string $baseUrl url to the swish api
     * @param object $handler guzzle http handler
     * @return Client
     */
    public static function make($rootCert, $clientCert, $baseUrl = self::SWISH_PRODUCTION_URL, $handler = null)
    {
        $config = [
            'verify' => $rootCert,
            'cert' => $clientCert,
            'handler' => HandlerStack::create(new CurlHandler()),
            'base_uri' => $baseUrl
        ];

        if(is_array($clientCert)) {
            $passphrase = $clientCert[1];
            $clientCert = $clientCert[0];
        }
        $certificateBody = file_get_contents($clientCert);
        if ($certificateBody) {
            $decodedCert = openssl_x509_parse($certificateBody, true);
            if ($decodedCert) {
                static::$certificateSerialNumber = $decodedCert['serialNumber'];
            }
        }

        $keyBody = file_get_contents($clientCert);
        if(isset($passphrase)) {
            $privateKey = openssl_pkey_get_private($keyBody, $passphrase);
        } else {
            $privateKey = openssl_pkey_get_private($keyBody);
        }
        if ($privateKey) {
            static::$certificatePrivateKey = $privateKey;
        }

        if ($handler) {
            $config['handler'] = $handler;
        }
        $guzzle = new \GuzzleHttp\Client($config);
        return new Client($guzzle, $baseUrl);
    }

}
