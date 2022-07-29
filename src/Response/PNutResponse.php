<?php declare(strict_types=1);

namespace PNut\Response;

use PNut\Stream\PNutStream;

class PNutResponse extends PNutBaseResponse
{
    public function __construct(PNutStream $stream)
    {
        parent::__construct($stream);
    }

    public function getResponse(bool $removeProtocolMessage = true): string
    {
        $response = parent::getRawResponse($removeProtocolMessage);

        if (parent::hasQuotes($response)) {
            return parent::getValueQuoted($response);
        }

        if ($this->isServerInfoResponse($response)) {
            return str_replace(" - http://www.networkupstools.org/", "", $response);
        }

        return parent::getValueUnquoted($response);
    }

    private function isServerInfoResponse(string $response): bool
    {
        return
            str_contains($response, "Network UPS Tools upsd") &&
            str_contains($response, "http://www.networkupstools.org/"
            );
    }
}
