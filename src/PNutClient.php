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

    /**
     * Client of the Network Ups Tools (NUT).
     * It creates a socket connection to the server to prepare request and get responses.
     */
    public function __construct()
    {
        $this->tryEncryption = true;
        $this->forceEncryption = false;
        $this->stream = null;
        $this->timeout = PNutStream::DEFAULT_TIMEOUT;
    }

    /**
     * Set timeout for the connection to the server.
     *
     * @param int $value Time in seconds before exit.
     * @return $this        The current instance.
     */
    public function setTimeout(int $value): PNutClient
    {
        $this->timeout = $value;

        return $this;
    }

    /**
     * To disable encryption for the connection to the server.
     * Otherwise, by default the client try to connect with encryption.
     *
     * @return $this    The current instance.
     */
    public function disableEncryption(): PNutClient
    {
        $this->tryEncryption = false;
        $this->forceEncryption = false;

        return $this;
    }

    /**
     * To force encryption for the connection to the server.
     * If the connection can't be encrypted, it will generate exception.
     *
     * @return $this    The current instance.
     */
    public function forceEncryption(): PNutClient
    {
        $this->forceEncryption = true;

        return $this;
    }

    /**
     * Initialize the connection to the server.
     * By default, it will try first to connect with encryption,
     * and then fall back to unencrypted if the server is not configure.
     *
     * @param string $serverAddress Address where the server is (for example an IP address or hostname).
     * @param int $serverPort       Port of where NUT server listen (default 3493).
     * @return PNutClient           The current instance.
     *
     * @throws Exception\Feature\FeatureNotConfiguredException
     * @throws Exception\Feature\FeatureNotSupportedException
     * @throws Exception\Socket\UnableToConnectSocketException
     * @throws Exception\Ssl\AlreadySslModeException
     * @throws Exception\Ssl\SslHandShakeException
     */
    public function connect(
        string $serverAddress,
        int    $serverPort = PNutStream::DEFAULT_SERVER_PORT,
    ): PNutClient
    {
        $this->stream = new PNutStream(
            $serverAddress,
            $serverPort,
            $this->tryEncryption,
            $this->forceEncryption,
            $this->timeout,
        );

        return $this;
    }

    /**
     * Get the socket stream to make some low-level actions.
     *
     * @return PNutStream   The current socket stream connection.
     */
    public function stream(): PNutStream
    {
        return $this->stream;
    }

    /**
     * To make some request to the server with the PNutRequest class.
     *
     * @return PNutRequest  An instance which allow requests to the server.
     */
    public function request(): PNutRequest
    {
        return new PNutRequest($this->stream);
    }
}
