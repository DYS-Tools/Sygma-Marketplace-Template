<?php

namespace App\Tests\Controller;

use App\Tests\BaseWebTest;

class DashboardControllerTest extends BaseWebTest
{
    public function testGetDashboardIndexWithAdmin(){
        $client = $this->login('yohanndurand76@gmail.com','dev') ;
        $client->request('GET', '/dashboard');
        $this->assertEquals(302, $client->getResponse()->getStatusCode());
    }
}