<?php

declare(strict_types=1);

namespace PNut\Exception\Socket;

use Throwable;
use Exception;

class UnableToConnectSocketException extends Exception
{
    public function __construct(string $socketMsg, int $socketCode, $code = 0, Throwable $previous = null)
    {
        parent::__construct("Unable to connect with socket (error $socketCode) : $socketMsg", $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [$this->code]: $this->message\n";
    }
}