<?php

namespace App\Tests\Unit;

use App\Http\VideoSignature;
use PHPUnit\Framework\TestCase;

class VideoSignatureTest extends TestCase
{
    public function testVideoSignature()
    {
        $class = new VideoSignature(1, 'TEST1234ENCRYPTION5678KEY');

        $uri = $class->getSignedPayloadUri(['test' => 'payload']);
        $this->assertSame(['test' => 'payload'], $class->getPayloadFromSignedUri($uri));
    }

    public function testVideoSignatureExpires()
    {
        $class = new VideoSignature(1, 'TEST1234ENCRYPTION5678KEY');
        $uri = $class->getSignedPayloadUri(['test' => 'payload']);

        sleep(2); // Payload expires after 1 second, lets wait 2 to be sure.

        $this->expectExceptionMessage('Invalid or expired uri.');
        $class->getPayloadFromSignedUri($uri);
    }
}