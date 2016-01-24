<?php
/**
 * Created by PhpStorm.
 * User: Johan
 * Date: 2016-01-23
 * Time: 22:10
 */

namespace HelmutSchneider\Swish\Tests;


use GuzzleHttp\Psr7\Response;
use HelmutSchneider\Swish\Util;

class UtilTest extends Test
{

    public function testGetPaymentRequestIdFromResponse()
    {
        $id = 'ABC123';
        $response = new Response(200, [
            'Location' => 'http://localhost/paymentrequests/' . $id,
        ]);

        $this->assertEquals($id, Util::getPaymentRequestIdFromResponse($response));
    }

    public function testGetRefundIdFromResponse()
    {
        $id = 'ABC123';
        $response = new Response(200, [
            'Location' => 'http://localhost/refunds/' . $id,
        ]);

        $this->assertEquals($id, Util::getRefundIdFromResponse($response));
    }

    public function testDecodeResponse()
    {
        $response = new Response(200, [], '{ "hello":"world" }');
        $decoded = Util::decodeResponse($response);

        $this->assertEquals(['hello' => 'world'], $decoded);
    }

}
