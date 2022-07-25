<?php

namespace PNut;

class PNutClientResponse
{
    public function __construct(
        private mixed $stream
    )
    {}

    public function getResponse(): array
    {
        $result = array();
        $rawLines = $this->getRawResponseList();

        foreach ($rawLines as $rawLine) {
            $value = $this->extractQuotationMarksWords($rawLine);
            $lineWithoutValue = str_replace(" \"{$value}\"", "", $rawLine);
            $restLineDump = explode(" ", $lineWithoutValue);
            $key = $restLineDump[count($restLineDump) - 1];

            if (isset($value)) {
                $result[$key] = $value;
            } else {
                $result[] = $key;
            }

        }

        return $result;
    }

    public function getRawResponseList(bool $isRemoveProtocolMessages = true): array
    {
        $lines = array();

        while($this->isReceiveDataFromStream())
        {

            $line = trim(fgets($this->stream, 128));

            if ($this->isEndResponse($line)) {
                return $lines;
            }

            if ($this->isNotContainProtocolsMsg($line, $isRemoveProtocolMessages)) {
                $lines[] = $line;
            }
        }

        return $lines;
    }

    public function getRawResponse(bool $removeProtocolMessages = true): string
    {
        $returnLine = "";
        $lines = $this->getRawResponseList($removeProtocolMessages);
        $linesCount = count($lines);

        for ($index = 0; $index < $linesCount; $index++)
        {
            $returnLine .= $lines[$index];

            if ($index != $linesCount - 1) {
                $returnLine .= "\n";
            }
        }

        return $returnLine;
    }

    // *********

    private function isReceiveDataFromStream(): bool
    {
        return !feof($this->stream);
    }

    private function isEndResponse(string $line): bool
    {
        return str_starts_with($line, "OK Goodbye");
    }

    private function isNotContainProtocolsMsg(string $line, bool $enable = true): bool
    {
        if (!$enable) {
            return true;
        }

        return
            !str_contains($line, "BEGIN LIST") &&
            !str_contains($line, "END LIST")
        ;
    }

    private function extractQuotationMarksWords(string $sentence): ?string
    {
        $matches = array();
        if (preg_match('/"([^"]+)"/', $sentence, $matches)) {
            return $matches[1];
        }

        return null;
    }
}