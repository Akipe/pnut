<?php

declare(strict_types=1);

namespace PNut\Response;

interface IPNutResponse
{
    public function getResponse(bool $removeProtocolMessage = true);
    public function getRawResponse(bool $removeProtocolMessage = true);
}