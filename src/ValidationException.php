<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-04-15
 * Time: 15:04
 */

namespace HelmutSchneider\Swish;
use Psr\Http\Message\ResponseInterface;

/**
 * Class ValidationException
 * @package HelmutSchneider\Swish
 */
class ValidationException extends \Exception
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
     * ValidationException constructor.
     * @param ResponseInterface $response
     */
    function __construct(ResponseInterface $response)
    {
        $this->response = $response;

        $data = json_decode(
            (string) $response->getBody(), true
        );
        $this->errors = [];

        foreach ($data as $item) {
            $this->errors[] = new Error($item);
        }

        $first = $this->errors[0];

        parent::__construct(
            sprintf('%s: %s', $first->errorCode, $first->errorMessage)
        );
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @return Error[]
     */
    public function getErrors()
    {
        return $this->errors;
    }

}
