<?php


namespace HelmutSchneider\Swish\Payout;


use HelmutSchneider\Swish\Client;

/**
 * Class PayoutRequestPayload
 *
 * @package HelmutSchneider\Swish\Payout
 */
class PayoutRequestPayload
{
    /**
     * @var string
     *
     * Example: 100.05
     * The amount to be paid.
     * Note that only period/dot (”.”) are accepted as decimal character with maximal 2 digits after.
     * Digits after separator are optional.
     */
    public $amount = '';
    /**
     * @var string
     *
     * The only supported value is: SEK
     * The currency to use.
     */
    public $currency = 'SEK';
    /**
     * @var string
     * YYYY-MM-DDTHH:MM:SS
     * Date and time for when the payout instruction was supplied.
     * Example: 2019-12-03T11:07:16
     */
    public $instructionDate = '';
    /**
     * @var string
     * 0-50 Alphanumeric characters.
     * Custom message.
     */
    public $message = '';
    /**
     * @var string
     * The mobile phone number of the person that receives the payment.
     */
    public $payeeAlias = '';
    /**
     * @var string
     * YYYYMMDDXXXX
     * The Social Security Number of the person that receives the payment.
     */
    public $payeeSSN = '';
    /**
     * @var string
     * Numeric, 10-11 digits
     * The Swish number of the merchant that makes the payout payment.
     */
    public $payerAlias = '';
    /**
     * @var string
     * 1-35 Alphanumeric characters.
     * A Merchant specific reference. This reference could for example be order id or similar.
     * The property is not used by Swish but is included in responses back to the client.
     */
    public $payerPaymentReference = '';
    /**
     * @var string
     * A UUID of length 32. All upper case hexadecimal characters.
     * A unique identifier created by the merchant to uniquely identify a payout instruction sent to the Swish system.
     * Swish uses this identifier to guarantee the uniqueness of the payout instruction and prevent occurrences of
     * unintended double payments.
     */
    public $payoutInstructionUUID;
    /**
     * @var string
     * Only supported value is: PAYOUT
     * Immediate payout.
     */
    public $payoutType = '';
    /**
     * @var string
     * Serial number of the signing certificate in hexadecimal format (without any leading ‘0x’ characters).
     * The public key of the certificate with this serial number will be used to verify the signature.
     */
    public $signingCertificateSerialNumber = '';

    /**
     * PayoutRequestPayload constructor.
     *
     * @param array $data
     */
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            if (property_exists($this, $key)) {
                $this->$key = $value;
            }
        }
        $this->signingCertificateSerialNumber = Client::$certificateSerialNumber;
        if (!isset($this->payoutInstructionUUID)) {
            $this->payoutInstructionUUID = strtoupper(md5(time()));
        }
        return $this;
    }
}
