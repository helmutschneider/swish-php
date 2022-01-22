<?php
namespace HelmutSchneider\Swish;

/**
 * Class CreatePaymentRequestResponse
 * @package HelmutSchneider\Swish
 */
class CreatePaymentRequestResponse
{

    /**
     * @var string
     */
    public $id = '';

    /**
     * @var string
     */
    public $paymentRequestToken = '';

    /**
     * CreatePaymentRequestResponse constructor.
     * @param string $id
     * @param string $paymentRequestToken
     */
    function __construct(string $id, string $paymentRequestToken)
    {
        $this->id = $id;
        $this->paymentRequestToken = $paymentRequestToken;
    }

}
