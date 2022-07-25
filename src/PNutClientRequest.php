<?php

namespace PNut;

class PNutClientRequest
{
    public function __construct(
        private mixed $stream
    )
    {}

    public function getNumLogins(
        string $upsName
    ): PNutClientRequest
    {
        fwrite(
            $this->stream,
            "GET NUMLOGINS {$upsName}\n"
        );

        return $this;
    }

    public function getUpsDesc(
        string $upsName
    ): PNutClientRequest
    {
        fwrite($this->stream, "GET UPSDESC {$upsName}\n");

        return $this;
    }

    public function getVar(
        string $upsName,
        string $varName
    ): PNutClientRequest
    {
        fwrite($this->stream, "GET VAR {$upsName} {$varName}\n");

        return $this;
    }

    public function getType(
        string $upsName,
        string $varName
    ): PNutClientRequest
    {
        fwrite($this->stream, "GET TYPE {$upsName} {$varName}\n");

        return $this;
    }

    public function getDesc(
        string $upsName,
        string $varName
    ): PNutClientRequest
    {
        fwrite($this->stream, "GET DESC {$upsName} {$varName}\n");

        return $this;
    }

    public function listUps(
    ): PNutClientRequest
    {
        //fwrite($this->stream, "LIST UPS\nLOGOUT\n");
        fwrite($this->stream, "LIST UPS\n");

        //return new PNutClientResponse($this->stream);
        return $this;
    }
    public function getNumLoginsTest(
        string $upsName
    ): PNutClientRequest
    {
        fwrite(
            $this->stream,
            "GET NUMLOGINS {$upsName}\n"
        );

        return $this;
    }


    public function listVar(
        string $upsName
    ): PNutClientRequest
    {
        fwrite($this->stream, "LIST VAR {$upsName}\n");

        return $this;
    }

    public function listEnum(
        string $upsName,
        string $varName
    ): PNutClientRequest
    {
        fwrite($this->stream, "LIST ENUM {$upsName} {$varName}\n");

        return $this;
    }

    public function listClient(
        string $upsName
    ): PNutClientRequest
    {
        fwrite($this->stream, "LIST CLIENT {$upsName}\n");

        return $this;
    }

    public function endRequest()
    {
        fwrite($this->stream, "LOGOUT\n");

        return new PNutClientResponse($this->stream);
    }
}