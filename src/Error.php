<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-04-15
 * Time: 14:25
 */

namespace HelmutSchneider\Swish;

/**
 * Class Error
 * @package HelmutSchneider\Swish
 */
class Error
{

    /**
     * @var string
     */
    public $errorCode = '';

    /**
     * @var string
     */
    public $errorMessage = '';

    /**
     * @var string
     */
    public $additionalInformation = '';

    /**
     * Error constructor.
     * @param string[] $data
     */
    function __construct(array $data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
    }

}