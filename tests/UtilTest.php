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

class UtilTest extends TestCase
{

    public function testGetObjectIdFromResponse(): void
    {
        $id = 'ABC123';
        $response = new Response(200, [
            'Location' => 'http://localhost/paymentrequests/' . $id,
        ]);

        $this->assertEquals($id, Util::getObjectIdFromResponse($response));
    }

}
