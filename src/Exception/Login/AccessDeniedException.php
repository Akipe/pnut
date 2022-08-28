<?php

declare(strict_types=1);

namespace PNut\Exception\Login;

use Exception;
use Throwable;

class AccessDeniedException extends Exception
{
    public const PROTOCOL_MESSAGE = "ACCESS-DENIED";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [$this->code]: $this->message\n";
    }
}