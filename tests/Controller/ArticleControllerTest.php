<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTest;

class ArticleControllerTest extends BaseWebTest
{
    public function testGetBlogListPage(){
        $client = static::createClient();
        $client->request('GET', '/article');
        $this->assertEquals(301, $client->getResponse()->getStatusCode());
    }
}