<?php declare(strict_types=1);

namespace PNut\Stream;

use PNut\Exception\Feature\FeatureNotConfiguredException;
use PNut\Exception\Feature\FeatureNotSupportedException;
use PNut\Exception\Request\ImpossibleSendRequestException;
use PNut\Exception\Socket\UnableToConnectSocketException;
use PNut\Exception\Ssl\AlreadySslModeException;
use PNut\Exception\Ssl\SslHandShakeException;
use PNut\Request\PNutRequest;

class PNutStream
{
    public const DEFAULT_SERVER_PORT = 3493;
    public const DEFAULT_TIMEOUT = 30;

    private mixed $stream;
    private ?int $errorCode;
    private ?string $errorMsg;
    private bool $isEncrypt;
    private ?string $protocolVersion;
    private ?string $serverVersion;

    /**
     * A socket stream class who manage the connection to the Network Ups Tools server.
     *
     * It allows to make low-level commands like send and receive messages.
     * It is used by PNutRequest & PNutResponse for communicating to the server with the procotol of Network UPS Tools.
     *
     * Documentation can be found at https://networkupstools.org/docs/developer-guide.chunked/ar01s09.html
     *
     * @param string $address       The server address (IP address or hostname).
     * @param int $port             The port number where NUT listen.
     * @param bool $tryEncryption   If we try to connect with encryption (TLS). It will fallback to unencrypted.
     * @param bool $forceEncryption Force encryption, so if it can't, it will generate an exception.
     * @param int $timeout          Time in seconds before timeout.
     *
     * @throws AlreadySslModeException
     * @throws FeatureNotConfiguredException
     * @throws FeatureNotSupportedException
     * @throws SslHandShakeException
     * @throws UnableToConnectSocketException
     * @throws ImpossibleSendRequestException
     */
    public function __construct(
        private readonly string $address,
        private readonly int    $port = PNutStream::DEFAULT_SERVER_PORT,
        bool                    $tryEncryption = true,
        bool                    $forceEncryption = false,
        private int             $timeout = PNutStream::DEFAULT_TIMEOUT,
    )
    {
        $this->errorCode = 0;
        $this->errorMsg = "";
        $this->isEncrypt = false;
        $this->protocolVersion = null;
        $this->serverVersion = null;

        $uri = "tcp://" . $this->address . ":" . $this->port;

        $context = $this->GetConfiguredStreamContext(
            $tryEncryption,
            $forceEncryption
        );

        $this->stream = stream_socket_client(
            $uri,
            $this->errorCode,
            $this->errorMsg,
            $this->timeout,
            STREAM_CLIENT_ASYNC_CONNECT | STREAM_CLIENT_CONNECT,
            $context
        );

        if (!$this->stream) {
            throw new UnableToConnectSocketException($this->errorMsg, $this->errorCode);
        }

        stream_set_blocking($this->stream, true);

        if ($tryEncryption || $forceEncryption) {
            $this->trySetEncryption($forceEncryption);
        }

        $this->setMetadata();
    }

    private function GetConfiguredStreamContext(bool $tryEncryption, bool $forceEncryption): mixed
    {
        // todo: add option for setting ssl methods
        $allowedProtocols = STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT;

        return stream_context_create(['ssl' => [
            "allow_self_signed"     => false,
            "verify_peer"           => false,
            "verify_peer_name"      => false,
            //"crypto_method"         => STREAM_CRYPTO_METHOD_TLS_SERVER,
            //'protocol_version'      => 'tls1',
            //"local_cert"            => "/path/to/my/server.pem",  // todo: add option for setting ssl certificate
            //"local_pk"              => "/path/to/my/private.key",
            //"honor_cipher_order"    => true,
            "crypto_method"         => $allowedProtocols
        ]]);
    }

    /**
     * It will try to enable TLS encryption for the connection, so all requests & responses will be encrypted.
     *
     * By default, it will fall back to unencrypted if the server is not configure.
     *
     * If needed it can be configured like forcing encryption end generating exception if it can't connect.
     *
     * @param bool $force Generate exception if the client can't encrypt the connection.
     * @return void
     *
     * @throws AlreadySslModeException
     * @throws FeatureNotConfiguredException
     * @throws FeatureNotSupportedException
     * @throws SslHandShakeException
     * @throws ImpossibleSendRequestException
     */
    private function trySetEncryption(bool $force): void
    {
        $response = $this
            ->send("STARTTLS")
            ->receive();

        if (str_contains($response, "OK STARTTLS")) {
            $isHandShakeSuccess = stream_socket_enable_crypto(
                $this->stream,
                true,
                STREAM_CRYPTO_METHOD_TLSv1_3_CLIENT |
                STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT // todo: add option for setting tls methods
                // | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT
            );

            if (!$isHandShakeSuccess) {
                fclose($this->stream);
                throw new SslHandShakeException();
            }

            $this->isEncrypt = true;

            return;
        }

        if ($force) {
            if (str_contains($response, FeatureNotSupportedException::PROTOCOL_MESSAGE)) {
                throw new FeatureNotSupportedException();
            }
            if (str_contains($response, FeatureNotConfiguredException::PROTOCOL_MESSAGE)) {
                throw new FeatureNotConfiguredException();
            }
            if (str_contains($response, AlreadySslModeException::PROTOCOL_MESSAGE)) {
                throw new AlreadySslModeException();
            }

            throw new \Exception("The stream is not encrypted");
        }

        $this->isEncrypt = false;
    }

    /**
     * Get the response of the NUT server, after a "send()" request.
     *
     * @param bool $removeProtocolMessages  To get or remove unneeded protocol message (by default disable).
     * @return string                       The message send by the NUT server.
     */
    public function receive(bool $removeProtocolMessages = true): string
    {
        $response = "";
        $thereIsStillData = false;

        do {
            $line = trim(fgets($this->stream, 128));

            if ($this->isEndResponse($line)) {
                return $response;
            }

            if (
                !$removeProtocolMessages ||
                $this->isNotContainProtocolsMsg($line)
            ) {
                $response .= $line . "\n";
            }

            if (str_contains($line, "BEGIN LIST")) {
                $thereIsStillData = true;
            }

            if (str_contains($line, "END LIST")) {
                $thereIsStillData = false;
            }
        } while ($thereIsStillData);

        return $this->removeLastReturnLine($response);
    }

    private function isEndResponse(string $line): bool
    {
        return str_starts_with($line, "OK Goodbye");
    }

    private function isNotContainProtocolsMsg(string $line): bool
    {
        return
            !str_contains($line, "BEGIN LIST") &&
            !str_contains($line, "END LIST");
    }

    private function removeLastReturnLine(string $response): string
    {
        if (str_ends_with($response, "\n")) {
            $response = substr_replace($response, "", -1);
        }
        return $response;
    }

    /**
     * Send a command to the NUT server.
     *
     * A list of commands can be found at https://networkupstools.org/docs/developer-guide.chunked/ar01s09.html
     *
     * To receive the response, use the "receive()" method.
     *
     * @param string $command The command send to NUT.
     * @return $this            The current socket stream instance.
     * @throws ImpossibleSendRequestException
     */
    public function send(string $command): PNutStream
    {
        set_error_handler(array($this, 'exception_error_handler'));
        $hasWrite = @fwrite($this->stream, "{$command}\n");
        restore_error_handler();

        if (!$hasWrite) {
            throw new ImpossibleSendRequestException($this);
        }

        return $this;
    }

    private function setMetadata(): void
    {
        $request = new PNutRequest($this);

        $this->protocolVersion = $request
            ->getProtocolVersion()
            ->getResponse();

        $serverInfo = explode(
            " ",
            $request
                ->getServerInformation()
                ->getResponse()
        );
        $this->serverVersion = $serverInfo[array_key_last($serverInfo)];
    }

    /**
     * Get the version of the NUT protocol used by the server.
     *
     * @return string The protocol version.
     */
    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    /**
     * Get the encryption status (TLS) of the connection.
     *
     * @return bool The status of the encryption.
     */
    public function isEncrypt(): bool
    {
        return $this->isEncrypt;
    }

    /**
     * Get the version of the NUT server application.
     *
     * @return string The NUT server application version.
     */
    public function getServerVersion(): string
    {
        return $this->serverVersion;
    }

    public function logout(): void
    {
        $this->send("LOGOUT");
    }

    public function getAddress(): string
    {
        return $this->address;
    }

    public function getPort(): int
    {
        return $this->port;
    }

    private function exception_error_handler(
        int $errno,
        string $errstr,
        string $errfile,
        int $errline
    ): bool
    {
        if (error_reporting()) {
            throw new ImpossibleSendRequestException(
                stream: $this,
                code: $errno,
                message: $errstr,
            );
        }
        return true;
    }
}
