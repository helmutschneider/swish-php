<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2016-01-23
 * Time: 18:46
 */

namespace HelmutSchneider\Swish;

use Psr\Http\Message\ResponseInterface;

/**
 * Class Util
 * @package HelmutSchneider\Swish
 */
class Util
{

    /**
     * @param ResponseInterface $response
     * @return string
     */
    public static function getObjectIdFromResponse(ResponseInterface $response)
    {
        $header = $response->getHeaderLine('Location');

        if (preg_match('/\/([^\/]+)$/', $header, $matches) === 1) {
            return $matches[1];
        }

        return '';
    }

    public static function getPaymentRequestIdsFromResponse(ResponseInterface $response) {
        $id = static::getObjectIdFromResponse($response);
        $paymentRequestToken = null;
        $header = $response->getHeaderLine('PaymentRequestToken');

        if (isset($header) && is_string($header) && strlen($header) > 0) {
            $paymentRequestToken = $header;
        }

        return [
            'id' => $id,
            'paymentRequestToken' => $paymentRequestToken
        ];
    }

}
