<?php declare(strict_types=1);

namespace PNut\Exception\Ssl;

use Throwable;

class SslHandShakeException extends \Exception
{
    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("Can't initialize the ssl handshake", $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
