<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTest;

class ProductControllerTest extends BaseWebTest
{
    // 200
    public function testGetReviewPageSucces(){
        $client = $this->login('yohanndurand76@gmail.com','dev') ;
        $client->request('GET', '/product/new');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}