<?php
/**
 * 
 */
namespace tests\Server;

use PHPUnit\Framework\TestCase;
use Server\Server;
use function Server\WithAddress;
use function Server\WithMaxConnections;
use function Server\FromArray;
use function Server\FromJSON;
/**
 * FunctionalOptionsTests is a test case for functional options
 * being called as part of Server constructor.
 */
class FunctionalOptionsTests extends TestCase
{

    public function testServerCreationWithoutOptionFunc()
    {
        $server = new Server();

        $this->assertEquals("127.0.0.1:8080", $server->getAddress());
    }

    public function testServerCreationWithAddress()
    {
        $expected = "192.168.0.1:8181";

        $server = new Server(WithAddress($expected));

        $this->assertEquals($expected, $server->getAddress());
    }

    public function testServerCreationWithMaxConnections()
    {
        $expected = 5;

        $server = new Server(WithMaxConnections($expected));

        $this->assertEquals($expected, $server->getMaxConnections());
    }

    public function testServerCreationWithCombined()
    {
        $expectedAddress = "192.168.0.1:8181";
        $expectedConnCount = 5;

        $server = new Server(
            WithAddress($expectedAddress),
            WithMaxConnections($expectedConnCount)
        );

        $this->assertEquals($expectedAddress, $server->getAddress());
        $this->assertEquals($expectedConnCount, $server->getMaxConnections());
    }

    public function testServerCreationFromArray()
    {
        $expected = array(
            'address' => '192.168.0.1:8181',
            'maxConnections' => 5
        );

        $server = new Server(FromArray($expected));

        $this->assertEquals($expected['address'], $server->getAddress());
        $this->assertEquals($expected['maxConnections'], $server->getMaxConnections());
    }

    public function testServerCreationFromJSON()
    {
        $expected = array(
            'address' => '192.168.0.1:8181',
            'maxConnections' => 5
        );

        $server = new Server(FromJSON(json_encode($expected)));

        $this->assertEquals($expected['address'], $server->getAddress());
        $this->assertEquals($expected['maxConnections'], $server->getMaxConnections());
    }
}
