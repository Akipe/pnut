<?php


use PNut\PNutCommand;
use PNut\PNutClient;

require __DIR__ . '/vendor/autoload.php';

$protocol = new PNutClient(
    "10.0.4.20",
    3493
);

$result = $protocol
    ->setTimeout(2)
    ->connect()
    ->listUps()
    ->getNumLoginsTest("ups")
    ->getVar("ups", "ups.firmware")
    ->endRequest()
    ->getResponse();

var_dump($result);
