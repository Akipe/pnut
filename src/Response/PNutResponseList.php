<?php declare(strict_types=1);

namespace PNut\Response;

class PNutResponseList extends PNutBaseResponse
{
    public function __construct(mixed $stream)
    {
        parent::__construct($stream);
    }

    public function getResponse(bool $removeProtocolMessage = true): array
    {
        $result = array();
        $responseArray = parent::getRawResponseArray($removeProtocolMessage);

        foreach ($responseArray as $response) {
            if (parent::hasQuotes($response)) {
                $value = parent::getValueQuoted($response);
                $key = parent::getPropertyName($response);

                $result[$key] = $value;
            } else if ($this->isHelpCommand($response)) {
                return $this->getHelpResponse($response);
            } else {
                $result[] = parent::getValueUnquoted($response);
            }
        }

        return $result;
    }

    private function isHelpCommand(string $response): bool
    {
        return str_contains($response, "Commands:");
    }

    private function getHelpResponse(string $response): array
    {
        $response = str_replace("Commands: ", "", $response);
        return explode(" ", $response);
    }

}