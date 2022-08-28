<?php

declare(strict_types=1);

namespace PNut\Exception\Request;

use PNut\Stream\PNutStream;
use Throwable;
use Exception;

class ImpossibleSendRequestException extends Exception
{
    public function __construct(PNutStream $stream, $code = 0, Throwable $previous = null, string $message = "")
    {
        parent::__construct(
            "Impossible to send command to the server.
            Are server parameters correctly configure ({$stream->getAddress()}:{$stream->getPort()}) ?\n$message",
            $code,
            $previous
        );
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [$this->code]: $this->message\n";
    }
}