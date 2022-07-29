<?php declare(strict_types=1);

namespace PNut\Stream;

use PNut\Exception\Feature\FeatureNotConfiguredException;
use PNut\Exception\Feature\FeatureNotSupportedException;
use PNut\Exception\Socket\UnableToConnectSocketException;
use PNut\Exception\Ssl\AlreadySslModeException;
use PNut\Exception\Ssl\SslHandShakeException;
use PNut\Request\PNutRequest;

/**
 *  Protocol Network UPS Tools (port 3493)
 *  https://networkupstools.org/docs/developer-guide.chunked/ar01s09.html
 */

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
     * @throws UnableToConnectSocketException
     * @throws FeatureNotConfiguredException
     * @throws AlreadySslModeException
     * @throws FeatureNotSupportedException
     * @throws SslHandShakeException
     */
    public function __construct(
        private readonly string $address,
        private readonly int    $port = PNutStream::DEFAULT_SERVER_PORT,
        bool $tryEncryption = true,
        bool $forceEncryption = false,
        private int             $timeout = PNutStream::DEFAULT_TIMEOUT,
    )
    {
        $this->errorCode = 0;
        $this->errorMsg = "";
        $this->isEncrypt = false;
        $this->protocolVersion = null;
        $this->serverVersion = null;

        $uri = "tcp://".$this->address.":".$this->port;

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

        if ($tryEncryption || $forceEncryption) {
            $this->trySetEncryption($forceEncryption);
        }

        $this->setMetadata();
    }

    public function isEncrypt(): bool
    {
        return $this->isEncrypt;
    }

    public function writeRequest(
        string $command,
    ): PNutStream
    {
        fwrite(
            $this->stream,
            "{$command}\n"
        );

        return $this;
    }

    public function getResponse(
        bool $removeProtocolMessages = true,
    ): string
    {
        $response = "";
        $thereIsStillData = false;

        do
        {
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
        }
        while($thereIsStillData);

        return $this->removeLastReturnLine($response);
    }

    public function getProtocolVersion(): string
    {
        return $this->protocolVersion;
    }

    public function getServerVersion(): string
    {
        return $this->serverVersion;
    }

    /**
     * @throws SslHandShakeException
     * @throws FeatureNotSupportedException
     * @throws FeatureNotConfiguredException
     * @throws AlreadySslModeException
     * @throws \Exception
     */
    private function trySetEncryption(bool $force): void
    {
        $response = $this
            ->writeRequest("STARTTLS")
            ->getResponse()
        ;

        if (str_contains($response, "OK STARTTLS")) {
            $isHandShakeSuccess = stream_socket_enable_crypto(
                $this->stream,
                true,
                STREAM_CRYPTO_METHOD_ANY_CLIENT
            );

            if(!$isHandShakeSuccess) {
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

    private function removeLastReturnLine(string $response): string
    {
        if (str_ends_with($response, "\n")) {
            $response = substr_replace($response, "", -1);
        }
        return $response;
    }

    private function isEndResponse(string $line): bool
    {
        return str_starts_with($line, "OK Goodbye");
    }

    private function isNotContainProtocolsMsg(string $line): bool
    {
        return
            !str_contains($line, "BEGIN LIST") &&
            !str_contains($line, "END LIST")
        ;
    }

    private function setMetadata(): void
    {
        $request = new PNutRequest($this);

        $this->protocolVersion = $request
            ->getProtocolVersion()
            ->getResponse()
        ;

        $serverInfo = explode(
            " ",
            $request
                ->getServerInformation()
                ->getResponse()
        );
        $this->serverVersion = $serverInfo[array_key_last($serverInfo)];
    }

    private function GetConfiguredStreamContext(
        bool $tryEncryption,
        bool $forceEncryption
    ): mixed
    {
        $context = stream_context_create();

        if ($tryEncryption || $forceEncryption) {
            \stream_context_set_option($context, 'ssl', 'allow_self_signed', false);
            \stream_context_set_option($context, 'ssl', 'verify_peer', false);
            \stream_context_set_option($context, 'ssl', 'verify_peer_name', false);
        }

        return $context;
    }
}
