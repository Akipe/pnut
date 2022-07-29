<?php declare(strict_types=1);

namespace PNut\Stream;

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
            throw new \Exception(
                "Error $this->errorCode : $this->errorCode"
            );
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

    private function trySetEncryption(bool $force): PNutStream
    {
        $response = $this
            ->writeRequest("STARTTLS")
            ->getResonse()
        ;

        // ok if response "OK STARTTLS"
        // error if response "ERR FEATURE-NOT-CONFIGURED"

        if (str_contains($response, "OK STARTTLS")) {
            $isHandShakeSuccess = stream_socket_enable_crypto(
                $this->stream,
                true,
                STREAM_CRYPTO_METHOD_ANY_CLIENT
            );

            if(!$isHandShakeSuccess) {
                fclose($this->stream);
                throw new \Exception(
                    "Error to decode tls data"
                );
            }

            $this->isEncrypt = true;

            return $this;
        }

        if ($force) {
            throw new \Exception("The stream is not encrypted");
        }

        $this->isEncrypt = false;

        return $this;
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
