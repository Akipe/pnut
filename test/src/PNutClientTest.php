<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use PNut\Exception\Ups\UnknownUpsException;
use PNut\Exception\Variable\VariableNotSupportedException;
use PNut\PNutClient;

final class PNutClientTest extends TestCase
{
    const TIMEOUT = 4;
    const ADDRESS = "127.0.0.1";

    public function testCanBeCreated(): void
    {
        $client = new PNutClient();

        $this->assertInstanceOf(PNutClient::class, $client);
    }

    public function testCanConnect(): void
    {
        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        $this->assertFalse($client->stream()->isEncrypt());

        $client->request()->logout();
    }

    public function testConnectionMetadata(): void
    {
        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        var_dump($client->stream()->getServerVersion());

        $this->assertEquals(
            "1.3",
            $client->stream()->getProtocolVersion()
        );

        $client->request()->logout();
    }

    public function testRequestListUps(): void
    {
        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        $listUps = $client->request()->listUps()->getResponse();

        $this->assertArrayHasKey("dummy-sim", $listUps);
        $this->assertArrayHasKey("dummy-seq", $listUps);

        $client->request()->logout();
    }

    public function testRequestClientsOfUps(): void
    {
        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        $listClient = $client->request()->getClients("dummy-sim")->getResponse();

        $this->assertIsArray($listClient);

        $client->request()->logout();
    }

    public function testRequestSingleVariable(): void
    {
        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        $upsFirmware = $client
            ->request()
            ->getVariable(
                "dummy-sim",
                "ups.firmware"
            )
            ->getResponse();

        $this->assertEquals("01.01.00", $upsFirmware);

        $client->request()->logout();
    }

    public function testRequestSingleVariableUnknownUps(): void
    {
        $this->expectException(UnknownUpsException::class);

        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        $client
            ->request()
            ->getVariable(
                "ups_not_existing",
                "ups.firmware"
            )
            ->getResponse();
    }

    public function testRequestSingleVariableUnknownVariable(): void
    {
        $this->expectException(VariableNotSupportedException::class);

        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        $client
            ->request()
            ->getVariable(
                "dummy-sim",
                "variable_not_existing"
            )
            ->getResponse();
    }

    public function testRequestAllVariables(): void
    {
        $client = new PNutClient();
        $client
            ->setTimeout(PNutClientTest::TIMEOUT)
            ->connect(PNutClientTest::ADDRESS);

        $allVariables = $client->request()->getAllVariables("dummy-sim")->getResponse();

        $expectedValues = array (
            'device.mfr' => 'EATON | Powerware',
            'device.model' => 'DBQ10634/5',
            'device.serial' => 'ADO6750531',
            'device.type' => 'pdu',
            'driver.name' => 'dummy-ups',
            'driver.parameter.mode' => 'dummy-once',
            'driver.parameter.pollinterval' => '1',
            'driver.parameter.port' => 'epdu-managed.dev',
            'driver.parameter.synchronous' => 'auto',
            'driver.version' => '20220328-3481-gccbf3eb6c6',
            'driver.version.internal' => '0.15',
            'outlet.1.current' => '0.00',
            'outlet.1.current.maximum' => '0.00',
            'outlet.1.desc' => 'Outlet 1',
            'outlet.1.id' => '1',
            'outlet.1.power' => '0.00',
            'outlet.1.powerfactor' => '0.05',
            'outlet.1.realpower' => '0.00',
            'outlet.1.status' => 'on',
            'outlet.1.switchable' => '0.00',
            'outlet.1.voltage' => '247.00',
            'outlet.2.current' => '0.00',
            'outlet.2.current.maximum' => '0.16',
            'outlet.2.desc' => 'Outlet 2',
            'outlet.2.id' => '2',
            'outlet.2.power' => '0.00',
            'outlet.2.powerfactor' => '0.01',
            'outlet.2.realpower' => '0.00',
            'outlet.2.status' => 'on',
            'outlet.2.switchable' => '1.00',
            'outlet.2.voltage' => '247.00',
            'outlet.3.current' => '0.00',
            'outlet.3.current.maximum' => '0.16',
            'outlet.3.desc' => 'Outlet 3',
            'outlet.3.id' => '3',
            'outlet.3.power' => '0.00',
            'outlet.3.powerfactor' => '0.13',
            'outlet.3.realpower' => '0.00',
            'outlet.3.status' => 'on',
            'outlet.3.switchable' => '2.00',
            'outlet.3.voltage' => '247.00',
            'outlet.4.current' => '0.19',
            'outlet.4.current.maximum' => '0.56',
            'outlet.4.desc' => 'Outlet 4',
            'outlet.4.id' => '2',
            'outlet.4.power' => '46.00',
            'outlet.4.powerfactor' => '0.60',
            'outlet.4.realpower' => '28.00',
            'outlet.4.status' => 'on',
            'outlet.4.switchable' => '3.00',
            'outlet.4.voltage' => '247.00',
            'outlet.count' => '4.00',
            'outlet.current' => '0.19',
            'outlet.desc' => 'All outlets',
            'outlet.id' => '0',
            'outlet.power' => '46.00',
            'outlet.realpower' => '28.00',
            'outlet.voltage' => '247.00',
            'ups.firmware' => '01.01.00',
            'ups.id' => 'my_device234',
            'ups.macaddr' => 'my_device234',
            'ups.mfr' => 'EATON | Powerware',
            'ups.model' => 'DBQ10634/5',
            'ups.serial' => 'ADO6750531',
            'ups.status' => NULL,
            'ups.temperature' => '49.00',
        );

        $this->assertEqualsCanonicalizing($expectedValues, $allVariables);

        $client->request()->logout();
    }

    /*public function testCannotBeCreatedFromInvalidEmailAddress(): void
    {
        $this->expectException(InvalidArgumentException::class);

        Email::fromString('invalid');
    }

    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(
            'user@example.com',
            Email::fromString('user@example.com')
        );
    }*/
}