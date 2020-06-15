<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTest;

class FrontControllerTest extends BaseWebTest
{
    // 200
    public function testGetReviewPageSucces(){
        $client = $this->login('yohanndurand76@gmail.com','dev') ;
        $client->request('GET', '/review');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // 200
    public function testGetLegalPageSucces(){
        $client = $this->login('yohanndurand76@gmail.com','dev') ;
        $client->request('GET', '/legal');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // 200
    public function testGetFaqPageSucces(){
        $client = $this->login('yohanndurand76@gmail.com','dev') ;
        $client->request('GET', '/faq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

}