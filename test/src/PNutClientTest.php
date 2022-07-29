<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PNut\PNutClient;

final class PNutClientTest extends TestCase
{
    public function testCanBeCreated(): void
    {
        $client = new PNutClient();
        $this->assertInstanceOf(
            PNutClient::class,
            $client
        );
    }

    /*public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }*/
}