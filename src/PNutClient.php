<?php declare(strict_types=1);

namespace PNut;

use PNut\Request\PNutRequest;
use PNut\Stream\PNutStream;

class PNutClient
{
    private ?PNutStream $stream;
    private bool $tryEncryption;
    private bool $forceEncryption;
    private ?int $timeout;

    public function __construct()
    {
        $this->tryEncryption = true;
        $this->forceEncryption = false;
        $this->stream = null;
        $this->timeout = PNutStream::DEFAULT_TIMEOUT;
    }

    public function setTimeout(int $value): PNutClient
    {
        $this->timeout = $value;

        return $this;
    }

    public function disableEncryption(): PNutClient
    {
        $this->tryEncryption = false;
        $this->forceEncryption = false;

        return $this;
    }

    public function forceEncryption(): PNutClient
    {
        $this->forceEncryption = true;

        return $this;
    }

    /**
     * @throws Exception\Ssl\AlreadySslModeException
     * @throws Exception\Feature\FeatureNotSupportedException
     * @throws Exception\Feature\FeatureNotConfiguredException
     * @throws Exception\Socket\UnableToConnectSocketException
     * @throws Exception\Ssl\SslHandShakeException
     */
    public function connect(
        string $serverAddress,
        int $serverPort = PNutStream::DEFAULT_SERVER_PORT,
    ): PNutRequest
    {
        $this->stream = new PNutStream(
            $serverAddress,
            $serverPort,
            $this->tryEncryption,
            $this->forceEncryption,
            $this->timeout,
        );

        return new PNutRequest(
            $this->stream
        );
    }

    public function stream(): PNutStream
    {
        return $this->stream;
    }

    public function request(): PNutRequest
    {
        return new PNutRequest(
            $this->stream
        );
    }
}
