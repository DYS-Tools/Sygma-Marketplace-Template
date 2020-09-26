<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTest;

class FrontControllerTest extends BaseWebTest
{

    // 200
    public function testGetLegalPageSucces(){
        $client = static::createClient();
        $client->request('GET', '/legal');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // 200
    public function testGetFaqPageSucces(){
        $client = static::createClient();
        $client->request('GET', '/faq');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // 200
    public function testGetReviewPageSucces(){
        $client = static::createClient();
        $client->request('GET', '/review');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    // 200
    public function testGetHelpAuthorPageSucces(){
        //$client = $this->login('yohanndurand76@gmail.com','dev') ;
        $client = static::createClient();
        $client->request('GET', '/helpAuthor');
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}