<?php
/**
 * Created by PhpStorm.
 * User: johan
 * Date: 2018-04-15
 * Time: 13:41
 */

namespace HelmutSchneider\Swish;

/**
 * Class PaymentRequest
 * @package HelmutSchneider\Swish
 */
class PaymentRequest
{

    /**
     * @var string
     */
    public $id = '';

    /**
     * @var string
     */
    public $payeePaymentReference = '';

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
     * PaymentRequest constructor.
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
