<?php


use PNut\PNutClient;

require __DIR__ . '/vendor/autoload.php';


$client = new PNutClient();

$client
    ->setTimeout(10)
    //->forceEncryption()
    ->connect(
    "10.0.4.20",
);

if ($client->stream()->isEncrypt()) {
    echo "it is encrypt";
} else {
    echo "it is not encrypt";
}
echo "\n";

var_dump(
    $client
        ->request()
        ->getServerInformation()
        ->getResponse()
);

var_dump(
    $client
        ->request()
        ->getProtocolVersion()
        ->getResponse()
    );
var_dump(
    $client
        ->stream()->getProtocolVersion()
);
var_dump(
    $client
        ->stream()->getServerVersion()
);
var_dump(
    $client
        ->request()
        ->getProtocolActions()
        ->getResponse()
);

var_dump(
    $client
    ->request()
    ->listUps()
    ->getResponse()
);

var_dump(
    $client
    ->request()
    ->getClients("ups")
    ->getResponse()
);
var_dump(
    $client
    ->request()
    ->getAllVariables("ups")
    ->getResponse()
);
var_dump(
    $client
    ->request()
    ->getVariable("ups", "ups.firmware")
    ->getResponse()
);

$client->request()->logout();


echo "\n\n\n";



$client->setTimeout(10)->connect(
    "127.0.0.1",
);

if ($client->stream()->isEncrypt()) {
    echo "it is encrypt";
} else {
    echo "it is not encrypt";
}
echo "\n";

var_dump(
    $client
        ->stream()->getProtocolVersion()
);
var_dump(
    $client
        ->stream()->getServerVersion());
var_dump(
    $client
    ->request()
    ->getProtocolVersion()
    ->getResponse()
);

var_dump(
    $client
    ->request()
    ->listUps()
    ->getResponse()
);

var_dump(
    $client
    ->request()
    ->getClients("dummy-sim")
    ->getResponse()
);
var_dump(
    $client
    ->request()
    ->getAllVariables("dummy-sim")
    ->getResponse()
);
var_dump(
    $client
    ->request()
    ->getVariable("dummy-sim", "ups.firmware")
    ->getResponse()
);

$client->request()->logout();
