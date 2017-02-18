<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2016-01-17
 * Time: 20:06
 */

namespace HelmutSchneider\Swish;


use GuzzleHttp\ClientInterface;
use GuzzleHttp\Handler\CurlFactory;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\ResponseInterface;

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
     */
    protected function sendRequest($method, $endpoint, array $options = [])
    {
        return $this->client->request($method, $this->baseUrl . $endpoint, array_merge([
            'headers' => [
                'Content-Type' => self::CONTENT_TYPE_JSON,
                'Accept' => self::CONTENT_TYPE_JSON,
            ],
        ], $options));
    }

    /**
     * @param array $data Payment request data
     * @return ResponseInterface
     */
    public function createPaymentRequest(array $data)
    {
        return $this->sendRequest('POST', '/paymentrequests', [
            'json' => $data,
        ]);
    }

    /**
     * @param string $id Payment request id
     * @return ResponseInterface
     */
    public function getPaymentRequest($id)
    {
        return $this->sendRequest('GET', '/paymentrequests/' . $id);
    }

    /**
     * @param array $data Refund data
     * @return ResponseInterface
     */
    public function createRefund(array $data)
    {
        return $this->sendRequest('POST', '/refunds', [
            'json' => $data,
        ]);
    }

    /**
     * @param string $id Refund id
     * @return ResponseInterface
     */
    public function getRefund($id)
    {
        return $this->sendRequest('GET', '/refunds/' . $id);
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
