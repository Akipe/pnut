<?php

declare(strict_types=1);

namespace PNut\Exception\Feature;

use Throwable;
use Exception;

class FeatureNotConfiguredException extends Exception
{
    public const PROTOCOL_MESSAGE = "";

    public function __construct($code = 0, Throwable $previous = null)
    {
        parent::__construct("Feature not configured", $code, $previous);
    }

    public function __toString(): string
    {
        return __CLASS__ . ": [$this->code]: $this->message\n";
    }
}