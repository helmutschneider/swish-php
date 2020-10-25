<?php


namespace HelmutSchneider\Swish;


use HelmutSchneider\Swish\Payout\PayoutRequestPayload;
use RuntimeException;

/**
 * Class PayoutRequest
 *
 * @package HelmutSchneider\Swish
 */
class PayoutRequest
{
    /** @var PayoutRequestPayload */
    public $payload;
    /**
     *
     * @var string
     * Base64 encoded.
     * Signature of the hashed payload.
     */
    public $signature;
    /**
     * @var string
     * https://<host[:port]>/...
     * URL that Swish system will use to notify caller about the result of the payment request. The URL must use HTTPS.
     * If not set (or not provided in the payload) it is the responsibility of the caller to check the
     * status of the request using GET operation as described in chapter 10.
     */
    public $callbackUrl;

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
     * PayoutRequest constructor.
     * Note: this class automatically calculates and adds signature to request.
     * Make sure to create a client and set certificates before creating the class.
     *
     * @param array $data
     * @throws CertificateException
     * @returns string|false - The payoutInstructionUUID or false if payload was not supplied.
     */
    public function __construct($data = [])
    {
        if (!isset(Client::$certificatePrivateKey)) {
            throw new CertificateException('Missing payee certificate private key');
        } else if (!isset(Client::$certificateSerialNumber)) {
            throw new CertificateException('Missing payee certificate serial number');
        }

        if (isset($data['callbackUrl'])) {
            $this->callbackUrl;
        }

        if (isset($data['payload'])) {
            $payload = new PayoutRequestPayload($data['payload']);
            $payloadHash = hash('SHA512', json_encode($payload));
            $signedHash = $this->encrypt($payloadHash);
            $this->signature = base64_encode($signedHash);
            return $payload;
        }

        return false;
    }

    /**
     * @param string $payloadHash
     * @return string
     */
    private function encrypt($payloadHash = '')
    {
        $privateKey = Client::$certificatePrivateKey;

        if (!$privateKey) {
            throw new RuntimeException('Invalid private key or passphrase');
        }

        $encryptedHash = '';
        openssl_private_encrypt($payloadHash, $encryptedHash, $privateKey, OPENSSL_PKCS1_PADDING);

        return $encryptedHash;
    }
}
