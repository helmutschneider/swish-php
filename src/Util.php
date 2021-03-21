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
    public static function getObjectIdFromResponse(ResponseInterface $response): string
    {
        $header = $response->getHeaderLine('Location');

        if (preg_match('/\/([^\/]+)$/', $header, $matches) === 1) {
            return $matches[1];
        }

        return '';
    }

}
