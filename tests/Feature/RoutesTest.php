<?php

namespace App\Tests\Feature;

use App\Tests\BootsApp;
use Zend\Diactoros\ServerRequest;

class RoutesTest extends BootsApp
{
    public function testFetchRoute()
    {
        $response = $this->runRequest(new ServerRequest([], [], '/', 'GET', 'php://input', [], [], ['uri' => '/hello-world'] ));
        $this->assertResponseOk();
        $this->assertEquals('{"msg":"fetch"}', (string) $response->getBody());
    }
}