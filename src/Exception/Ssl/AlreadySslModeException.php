<?php declare(strict_types=1);

namespace PNut\Exception\Ssl;

use Throwable;

class AlreadySslModeException extends \Exception
{
    public const PROTOCOL_MESSAGE = "";

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("SSL is already used", $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}