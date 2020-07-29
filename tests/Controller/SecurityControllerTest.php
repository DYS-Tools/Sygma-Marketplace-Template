<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTest;

class SecurityControllerTest extends BaseWebTest
{
    // 200
    public function testGetReviewPageSucces(){
        $client = static::createClient();
        $client->request('GET', '/register');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}