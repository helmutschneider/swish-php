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
use Psr\Http\Message\ResponseInterface;

/**
 * Class Client
 * @package HelmutSchneider\Swish
 */
class Client
{
    const SWISH_PRODUCTION_URL = 'https://swicpc.bankgirot.se/swish-cpcapi/api/v1';
    const SWISH_TEST_URL = 'https://mss.swicpc.bankgirot.se/swish-cpcapi/api/v1';
    const CONTENT_TYPE_JSON = 'application/json';

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
     */
    protected function sendRequest($method, $endpoint, array $options = [])
    {
        try {
            return $this->client->request($method, $this->baseUrl . $endpoint, array_merge([
                'headers' => [
                    'Content-Type' => self::CONTENT_TYPE_JSON,
                    'Accept' => self::CONTENT_TYPE_JSON,
                ],
            ], $options));
        }
        catch (ClientException $e) {
            return $e->getResponse();
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
     * @param ResponseInterface $response
     * @throws ValidationException
     */
    protected function maybeThrowValidationException(ResponseInterface $response)
    {
        if ($response->getStatusCode() === 422) {
            throw new ValidationException($response);
        }
    }

    /**
     * @param PaymentRequest $request
     * @return string payment request id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException
     */
    public function createPaymentRequest(PaymentRequest $request)
    {
        $response = $this->sendRequest('POST', '/paymentrequests', [
            'json' => $this->filterRequestBody((array) $request),
        ]);

        $this->maybeThrowValidationException($response);

        return Util::getObjectIdFromResponse($response);
    }

    /**
     * @param string $id Payment request id
     * @return PaymentRequest
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getPaymentRequest($id)
    {
        $response = $this->sendRequest('GET', '/paymentrequests/' . $id);

        return new PaymentRequest(
            json_decode((string) $response->getBody(), true)
        );
    }

    /**
     * @param Refund $refund
     * @return string refund id
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws ValidationException
     */
    public function createRefund(Refund $refund)
    {
        $response = $this->sendRequest('POST', '/refunds', [
            'json' => $this->filterRequestBody((array) $refund),
        ]);

        $this->maybeThrowValidationException($response);

        return Util::getObjectIdFromResponse($response);
    }

    /**
     * @param string $id Refund id
     * @return Refund
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getRefund($id)
    {
        $response = $this->sendRequest('GET', '/refunds/' . $id);

        return new Refund(
            json_decode((string) $response->getBody(), true)
        );
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
        ];
        if ($handler) {
            $config['handler'] = $handler;
        }
        $guzzle = new \GuzzleHttp\Client($config);
        return new Client($guzzle, $baseUrl);
    }

}
