<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-04-15
 * Time: 13:58
 */

namespace HelmutSchneider\Swish;

/**
 * Class Refund
 * @package HelmutSchneider\Swish
 */
class Refund
{

    /**
     * @var string
     */
    public $id = '';

    /**
     * @var string
     */
    public $payerPaymentReference = '';

    /**
     * @var string
     */
    public $originalPaymentReference = '';

    /**
     * @var string
     */
    public $paymentReference = '';

    /**
     * @var string
     */
    public $callbackUrl = '';

    /**
     * @var string
     */
    public $payerAlias = '';

    /**
     * @var string
     */
    public $payeeAlias = '';

    /**
     * @var string
     */
    public $amount = '';

    /**
     * @var string
     */
    public $currency = 'SEK';

    /**
     * @var string
     */
    public $message = '';

    /**
     * @var string
     */
    public $status = '';

    /**
     * @var string
     */
    public $dateCreated = '';

    /**
     * @var string
     */
    public $datePaid = '';

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
     * Refund constructor.
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
