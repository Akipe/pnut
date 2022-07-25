<?php

/**
 *  Protocol Network UPS Tools (port 3493)
 *  https://networkupstools.org/docs/developer-guide.chunked/ar01s09.html
 */

namespace PNut;

class PNutClient
{
    public const DEFAULT_SERVER_PORT = 3493;
    public const DEFAULT_TIMEOUT = 30;

    private mixed $stream;
    private ?int $errorCode;
    private ?string $errorMsg;

    public function __construct(
        private string $serverAddress,
        private int $serverPort = PNutClient::DEFAULT_SERVER_PORT,
        private int $timeout = PNutClient::DEFAULT_TIMEOUT
    )
    {
        $this->stream = null;
        $this->errorCode = null;
        $this->errorMsg = null;
    }

    public function setTimeout(int $value): PNutClient
    {
        $this->timeout = $value;

        return $this;
    }

    public function connect(): PNutClientRequest
    {
        $stream = fsockopen(
            $this->serverAddress,
            $this->serverPort,
            $this->errorCode,
            $this->errorMsg,
            $this->timeout
        );

        if (!$stream) {
            throw new \Exception(
                "Error {$this->errorCode} : {$this->errorCode}"
            );
        }

        $this->stream = $stream;

        return new PNutClientRequest($this->stream);
    }

}