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
     * Payment reference supplied by the Merchant. This is not used by Swish but is included in responses back to the client.
     * This reference could for example be an order id or similar. If set the value must not exceed 35 characters
     * and only the following characters are allowed: [a-ö, A-Ö, 0-9, -]
     */
    public $payeePaymentReference = '';

    /**
     * @var string
     */
    public $paymentReference = '';

    /**
     * @var string
     * URL that Swish will use to notify caller about the result of the payment request. The URL has to use HTTPS.
     */
    public $callbackUrl = '';

    /**
     * @var string
     * The registered Cell phone number of the person that makes the payment. It can only contain numbers and has
     * to be at least 8 and at most 15 digits. It also needs to match the following format in order to be found in
     * Swish: country code + cell phone number (without leading zero). E.g.: 46712345678 If set, request is handled
     * as E-Commerce payment. If not set, request is handled as MCommerce payment.
     */
    public $payerAlias = '';

    /**
     * @var string
     * The Swish number of the payee. It needs to match with Merchant Swish number.
     */
    public $payeeAlias = '';

    /**
     * @var string
     * cannot be less than 0.01 SEK and not more than 999999999999.99 SEK. Valid value has to be all digits or
     * with 2 digit decimal separated with a period.
     */
    public $amount = '';

    /**
     * @var string
     * The currency to use. Currently the only supported value is SEK.
     */
    public $currency = 'SEK';

    /**
     * @var string
     * Merchant supplied message about the payment/order. Max 50 characters. Allowed characters are the letters a-ö,
     * A-Ö, the numbers 0-9 and any of the special characters :;.,?!()-”.
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
     * @var string
     * The social security number of the individual making the payment, should match the registered value for payerAlias
     * or the payment will not be accepted. The value should be a proper Swedish social security number
     *  (personnummer or sammordningsnummer).
     */
    public $payerSSN = '';

    /**
     * @var string
     * Minimum age (in years) that the individual connected to the payerAlias has to be in order for the payment
     * to be accepted. Value has to be in the range of 1 to 99.
     */
    public $ageLimit = '';

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
