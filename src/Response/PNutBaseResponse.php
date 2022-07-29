<?php declare(strict_types=1);

namespace PNut\Response;

use PNut\Stream\PNutStream;

class PNutBaseResponse
{
    private const RULE_BETWEEN_QUOTE = '/"([^"]+)"/';

    public function __construct(
        public readonly PNutStream $stream,
    )
    {}

    public function getRawResponseArray(bool $isRemoveProtocolMessages = true): array
    {
        return explode(
            "\n",
            $this->getRawResponse($isRemoveProtocolMessages)
        );
    }

    public function getRawResponse(bool $removeProtocolMessages = true): string
    {
        return $this->stream->receive($removeProtocolMessages);
    }

    // *********

    protected function hasQuotes(string $response): bool
    {
        return str_contains($response, '"');
    }

    protected function getPropertyName(string $response): string
    {
        $indexQuote = strpos($response, '"') - 1;
        $responseWithoutValue = substr_replace($response, "", $indexQuote);

        return $this->getLastWord($responseWithoutValue);
    }

    protected function getValueQuoted(string $response): ?string
    {
        $matches = array();

        if (preg_match(
            PNutBaseResponse::RULE_BETWEEN_QUOTE,
            $response,
            $matches)
        ) {
            return $matches[1];
        }

        return null;
    }

    protected function getValueUnquoted(string $response): ?string
    {
        return $this->getLastWord($response);
    }

    // *********

    private function getLastWord(string $sentence): string
    {
        $sentenceWords = explode(" ", $sentence);
        return $sentenceWords[array_key_last($sentenceWords)];
    }
}
