<?php

declare(strict_types=1);

namespace PNut\Exception\Request;

use Throwable;
use Exception;

class InvalidArgumentRequestException extends Exception
{
    public const PROTOCOL_MESSAGE = "INVALID-ARGUMENT";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [$this->code]: $this->message\n";
    }
}