<?php declare(strict_types=1);

namespace PNut\Request;

use PNut\Response\PNutResponse;
use PNut\Response\PNutResponseList;
use PNut\Stream\PNutStream;

class PNutRequest
{
    public function __construct(
        public readonly PNutStream $stream,
    )
    {}

    public function getProtocolVersion(): PNutResponse
    {
        $this->stream->writeRequest(
            "NETVER"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    public function getServerInformation(): PNutResponse
    {
        $this->stream->writeRequest(
            "VER"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    public function getProtocolActions(): PNutResponseList
    {
        $this->stream->writeRequest(
            "HELP"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }

    public function getNumberLogins(
        string $upsName
    ): PNutResponse
    {
        $this->stream->writeRequest(
            "GET NUMLOGINS {$upsName}"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    public function getUpsDescription(
        string $upsName
    ): PNutResponse
    {
        $this->stream->writeRequest(
            "GET UPSDESC {$upsName}"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    public function getVariable(
        string $ups,
        string $name
    ): PNutResponse
    {
        $this->stream->writeRequest(
            "GET VAR {$ups} {$name}"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    public function getVariableType(
        string $upsName,
        string $varName
    ): PNutResponse
    {
        $this->stream->writeRequest(
            "GET TYPE {$upsName} {$varName}"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    public function getVariableDescription(
        string $upsName,
        string $varName
    ): PNutResponse
    {
        $this->stream->writeRequest(
            "GET DESC {$upsName} {$varName}"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    public function getUpsCommandDescription(
        string $ups,
        string $command
    ): PNutResponse
    {
        $this->stream->writeRequest(
            "GET CMDDESC {$ups} {$command}"
        );

        return new PNutResponse(
            $this->stream,
        );
    }

    // todo: there is 2 value quoted
    /*public function getVariableRange(
        string $ups,
        string $variable
    ): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST RANGE {$ups} {$variable}"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }*/

    public function listUps(
    ): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST UPS"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }

    public function getAllVariables(
        string $upsName
    ): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST VAR {$upsName}"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }

    public function getAllEditableVariables(
        string $ups
    ): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST RW {$ups}"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }

    public function getAllUpsCommands(
        string $ups
    ): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST CMD {$ups}"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }

    public function getVariableEnumeration(
        string $upsName,
        string $varName
    ): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST ENUM {$upsName} {$varName}"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }

    public function getClients(
        string $upsName
    ): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST CLIENT {$upsName}"
        );

        return new PNutResponseList(
            $this->stream,
        );
    }

    public function logout(): PNutResponse
    {
        $this->stream->writeRequest(
            "LOGOUT"
        );

        return new PNutResponse(
            $this->stream,
        );
    }
}