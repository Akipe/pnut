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
    {
    }

    public function getProtocolVersion(): PNutResponse
    {
        $this->stream->send(
            "NETVER"
        );

        return new PNutResponse($this->stream);
    }

    public function getServerInformation(): PNutResponse
    {
        $this->stream->send(
            "VER"
        );

        return new PNutResponse($this->stream);
    }

    public function getProtocolActions(): PNutResponseList
    {
        $this->stream->send(
            "HELP"
        );

        return new PNutResponseList($this->stream);
    }

    public function getNumberLogins(string $ups): PNutResponse
    {
        $this->stream->send(
            "GET NUMLOGINS {$ups}"
        );

        return new PNutResponse($this->stream);
    }

    public function getUpsDescription(string $ups): PNutResponse
    {
        $this->stream->send(
            "GET UPSDESC {$ups}"
        );

        return new PNutResponse($this->stream);
    }

    public function getVariable(string $ups, string $name): PNutResponse
    {
        $this->stream->send(
            "GET VAR {$ups} {$name}"
        );

        return new PNutResponse($this->stream);
    }

    public function getVariableType(string $ups, string $name): PNutResponse
    {
        $this->stream->send(
            "GET TYPE {$ups} {$name}"
        );

        return new PNutResponse($this->stream);
    }

    public function getVariableDescription(string $ups, string $name): PNutResponse
    {
        $this->stream->send(
            "GET DESC {$ups} {$name}"
        );

        return new PNutResponse($this->stream);
    }

    public function getUpsCommandDescription(string $ups, string $command): PNutResponse
    {
        $this->stream->send(
            "GET CMDDESC {$ups} {$command}"
        );

        return new PNutResponse($this->stream);
    }

    // todo: there is 2 value quoted
    /*public function getVariableRange(string $ups, string $variable): PNutResponseList
    {
        $this->stream->writeRequest(
            "LIST RANGE {$ups} {$variable}"
        );

        return new PNutResponseList($this->stream);
    }*/

    public function listUps(): PNutResponseList
    {
        $this->stream->send(
            "LIST UPS"
        );

        return new PNutResponseList($this->stream);
    }

    public function getAllVariables(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST VAR {$ups}"
        );

        return new PNutResponseList($this->stream);
    }

    public function getAllEditableVariables(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST RW {$ups}"
        );

        return new PNutResponseList($this->stream);
    }

    public function getAllUpsCommands(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST CMD {$ups}"
        );

        return new PNutResponseList($this->stream);
    }

    public function getVariableEnumeration(string $ups, string $name): PNutResponseList
    {
        $this->stream->send(
            "LIST ENUM {$ups} {$name}"
        );

        return new PNutResponseList($this->stream);
    }

    public function getClients(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST CLIENT {$ups}"
        );

        return new PNutResponseList($this->stream);
    }

    public function logout(): PNutResponse
    {
        $this->stream->send(
            "LOGOUT"
        );

        return new PNutResponse($this->stream);
    }
}