<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2016-01-17
 * Time: 20:06
 */

namespace HelmutSchneider\Swish;


use GuzzleHttp\ClientInterface;
use Psr\Http\Message\ResponseInterface;

class Client
{
    const JSON_CONTENT_TYPE = 'application/json';

    /**
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * @var string
     */
    private $baseUrl;

    /**
     * @var array
     */
    private $options;

    /**
     * Client constructor.
     * @param ClientInterface $client
     * @param string $baseUrl
     * @param array $options guzzle options
     */
    function __construct(ClientInterface $client, $baseUrl, array $options)
    {
        $this->client = $client;
        $this->baseUrl = $baseUrl;
        $this->options = $options;
    }

    /**
     * @param string $method HTTP-method
     * @param string $endpoint
     * @param array $options guzzle options
     * @return ResponseInterface
     */
    protected function sendRequest($method, $endpoint, array $options = [])
    {
        return $this->client->request($method, $this->baseUrl . $endpoint, array_merge_recursive([
            'headers' => [
                'Content-Type' => self::JSON_CONTENT_TYPE,
                'Accept' => self::JSON_CONTENT_TYPE,
            ],
        ], $this->options, $options));
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

}
