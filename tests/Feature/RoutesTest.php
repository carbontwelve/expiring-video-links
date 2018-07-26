<?php

namespace App\Tests\Feature;

use App\Tests\BootsApp;
use Zend\Diactoros\ServerRequest;

class RoutesTest extends BootsApp
{
    public function testIndexRoute()
    {
        $response = $this->runRequest(new ServerRequest([], [], '/', 'GET', 'php://input', [], [], ['uri' => '/'] ));
        $this->assertResponseOk();
        // $this->assertEquals('{"msg":"fetch"}', (string) $response->getBody());
    }
}