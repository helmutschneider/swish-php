<?php


namespace HelmutSchneider\Swish;

/**
 * Class QRCodeRequest
 *
 * @property string $token - The payment request token to return QR Code for.
 * @property string $format - File format to return. Possible values: jpg, png, svg
 * @property number $size - The width/height of the square QR code, in pixels. Optional if format is SVG.
 * @property number $border - Width of the border. Optional.
 * @property boolean $transparent - Should the QR code have a transparent background? Optional.
 *
 * @package HelmutSchneider\Swish
 */
class QRCodeRequest
{
    public $token;
    public $format;
    public $size;
    public $border;
    public $transparent;
}
