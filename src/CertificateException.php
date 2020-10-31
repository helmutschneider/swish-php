<?php

namespace HelmutSchneider\Swish;

use Psr\Http\Message\ResponseInterface;

/**
 * Class CertificateException
 *
 * @package HelmutSchneider\Swish
 */
class CertificateException extends \Exception
{

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var Error[]
     */
    protected $errors;

    /**
     * CertificateException constructor.
     *
     * @param string $message
     * @param null $statusCode
     */
    function __construct($message = 'There was a problem with the given certificate. Please check so that the Swish number in the certificate is enrolled.', $statusCode = null)
    {
        if (isset($statusCode)) {
            parent::__construct(
                sprintf('%s %s:', $statusCode, is_string($message) ? $message : $message->getBody())
            );
        } else {
            parent::__construct(
                sprintf('%s:', is_string($message) ? $message : $message->getBody())
            );
        }
    }

}
