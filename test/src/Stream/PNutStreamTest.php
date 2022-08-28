<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PNut\Exception\Request\ImpossibleSendRequestException;
use PNut\Stream\PNutStream;

final class PNutStreamTest extends TestCase
{
    private const TIMEOUT = 4;
    public function canBeCreated(): void
    {
        $stream = new PNutStream(
            address: "127.0.0.1",
            timeout: PNutStreamTest::TIMEOUT
        );

        $this->assertInstanceOf(PNutStream::class, $stream);
    }

    public function testConnection(): void
    {
        $stream = new PNutStream(
            address: "127.0.0.1",
            timeout: PNutStreamTest::TIMEOUT
        );

        $this->assertFalse($stream->isEncrypt());

        $stream->logout();
    }

    public function testConnectionUnableToConnectSocket(): void
    {
        $this->expectException(ImpossibleSendRequestException::class);

        new PNutStream(
            address: "0.0.0.0",
            port: 44462,
            timeout: 4
        );

        $this->fail("No ". ImpossibleSendRequestException::class . " generated.");
    }
}