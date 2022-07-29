<?php declare(strict_types=1);

namespace PNut\Exception\Variable;

use Throwable;

class TooLongVariableValueException extends \Exception
{
    public const PROTOCOL_MESSAGE = "TOO-LONG";

    public function __construct($message, $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}