<?php declare(strict_types=1);

namespace PNut\Response;

use PNut\Exception\Ups\UnknownUpsException;
use PNut\Exception\Variable\VariableNotSupportedException;

class PNutResponseError
{
    /**
     * @throws UnknownUpsException
     */
    public static function hasResponseUnknownUps(IPNutResponse $response): void
    {
        if (str_contains(
            $response->getRawResponse(),
            UnknownUpsException::PROTOCOL_MESSAGE)
        ) {
            throw new UnknownUpsException("Unknown UPS");
        }
    }

    /**
     * @throws VariableNotSupportedException
     */
    public static function hasResponseVariableNotSupported(IPNutResponse $response): void
    {
        if (
            str_contains(
                $response->getRawResponse(),
                VariableNotSupportedException::PROTOCOL_MESSAGE)
        ) {
            throw new VariableNotSupportedException("Variable not supported");
        }
    }
}