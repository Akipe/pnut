<?php declare(strict_types=1);

namespace PNut\Request;

use PNut\Exception\Ups\UnknownUpsException;
use PNut\Exception\Variable\VariableNotSupportedException;
use PNut\Response\PNutResponse;
use PNut\Response\PNutResponseError;
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

    /**
     * @throws UnknownUpsException
     */
    public function getNumberLogins(string $ups): PNutResponse
    {
        $this->stream->send(
            "GET NUMLOGINS {$ups}"
        );

        $response = new PNutResponse($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);

        return $response;
    }

    /**
     * @throws UnknownUpsException
     */
    public function getUpsDescription(string $ups): PNutResponse
    {
        $this->stream->send(
            "GET UPSDESC {$ups}"
        );

        $response = new PNutResponse($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);

        return $response;
    }

    /**
     * @throws VariableNotSupportedException
     * @throws UnknownUpsException
     */
    public function getVariable(string $ups, string $name): PNutResponse
    {
        $this->stream->send(
            "GET VAR {$ups} {$name}"
        );

        $response = new PNutResponse($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);
        PNutResponseError::hasResponseVariableNotSupported($response);

        return $response;
    }

    /**
     * @throws VariableNotSupportedException
     * @throws UnknownUpsException
     */
    public function getVariableType(string $ups, string $name): PNutResponse
    {
        $this->stream->send(
            "GET TYPE {$ups} {$name}"
        );

        $response = new PNutResponse($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);
        PNutResponseError::hasResponseVariableNotSupported($response);

        return $response;
    }

    /**
     * @throws VariableNotSupportedException
     * @throws UnknownUpsException
     */
    public function getVariableDescription(string $ups, string $name): PNutResponse
    {
        $this->stream->send(
            "GET DESC {$ups} {$name}"
        );

        $response = new PNutResponse($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);
        PNutResponseError::hasResponseVariableNotSupported($response);

        return $response;
    }

    /**
     * @throws UnknownUpsException
     */
    public function getUpsCommandDescription(string $ups, string $command): PNutResponse
    {
        $this->stream->send(
            "GET CMDDESC {$ups} {$command}"
        );

        $response = new PNutResponse($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);

        return $response;
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

    /**
     * @throws UnknownUpsException
     */
    public function getAllVariables(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST VAR {$ups}"
        );

        $response = new PNutResponseList($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);

        return $response;
    }

    /**
     * @throws UnknownUpsException
     */
    public function getAllEditableVariables(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST RW {$ups}"
        );

        $response = new PNutResponseList($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);

        return $response;
    }

    /**
     * @throws UnknownUpsException
     */
    public function getAllUpsCommands(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST CMD {$ups}"
        );

        $response = new PNutResponseList($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);

        return $response;
    }

    /**
     * @throws VariableNotSupportedException
     * @throws UnknownUpsException
     */
    public function getVariableEnumeration(string $ups, string $name): PNutResponseList
    {
        $this->stream->send(
            "LIST ENUM {$ups} {$name}"
        );

        $response = new PNutResponseList($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);
        PNutResponseError::hasResponseVariableNotSupported($response);

        return $response;
    }

    /**
     * @throws UnknownUpsException
     */
    public function getClients(string $ups): PNutResponseList
    {
        $this->stream->send(
            "LIST CLIENT {$ups}"
        );

        $response = new PNutResponseList($this->stream);

        PNutResponseError::hasResponseUnknownUps($response);

        return $response;
    }

    public function logout(): PNutResponse
    {
        $this->stream->send(
            "LOGOUT"
        );

        return new PNutResponse($this->stream);
    }
}