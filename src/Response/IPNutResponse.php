<?php declare(strict_types=1);

namespace PNut\Response;

interface IPNutResponse
{
    function getResponse(bool $removeProtocolMessage = true);
    function getRawResponse(bool $removeProtocolMessage = true);
}