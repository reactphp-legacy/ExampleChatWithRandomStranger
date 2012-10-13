<?php

namespace Igorw\Chatroulette;

use Monolog\Logger;
use Monolog\Handler\TestHandler;

class ChatrouletteTest extends \PHPUnit_Framework_TestCase
{
    private $logger;
    private $handler;

    public function setUp()
    {
        $this->handler = new TestHandler();

        $this->logger = new Logger('chatroulette');
        $this->logger->pushHandler($this->handler);

        $this->chatroulette = new Chatroulette($this->logger);
    }

    /** @test */
    public function connectingOneUserShouldMakeHimWait()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $this->chatroulette->connect($alice);

        $this->assertConnectionData($alice, [
            'Hello Alice!',
            'Please wait until a partner connects.',
        ]);

        $this->assertLogs([
            'New connection Alice',
        ]);
    }

    /** @test */
    public function connectingTwoUsersShouldPairThemUp()
    {
        list($alice, $bob) = $this->createAliceAndBob();

        $this->chatroulette->connect($alice);
        $this->chatroulette->connect($bob);

        $this->assertConnectionData($alice, [
            'Hello Alice!',
            'Please wait until a partner connects.',
            'You are now talking to Bob.',
        ]);

        $this->assertConnectionData($bob, [
            'Hello Bob!',
            'You are now talking to Alice.',
        ]);

        $this->assertLogs([
            'New connection Alice',
            'New connection Bob',
            'Pairing up connection Bob with waiting connection Alice',
        ]);
    }

    /** @test */
    public function disconnectingAliceShouldDisconnectBob()
    {
        list($alice, $bob) = $this->createAliceAndBob();

        $this->chatroulette->connect($alice);
        $this->chatroulette->connect($bob);

        $alice->close();

        $this->assertClosed($alice);
        $this->assertClosed($bob);
    }

    /** @test */
    public function disconnectingBobShouldDisconnectAlice()
    {
        list($alice, $bob) = $this->createAliceAndBob();

        $this->chatroulette->connect($alice);
        $this->chatroulette->connect($bob);

        $bob->close();

        $this->assertClosed($alice);
        $this->assertClosed($bob);
    }

    private function createAliceAndBob()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $bob = new ConnectionStub();
        $bob->id = 'Bob';

        return array($alice, $bob);
    }

    private function assertClosed($conn)
    {
        $this->assertFalse($conn->isReadable());
        $this->assertFalse($conn->isWritable());
    }

    private function assertConnectionData($conn, $expected)
    {
        $data = implode("\n", $expected)."\n";
        $this->assertSame($data, $conn->data);
    }

    private function assertLogs($expected)
    {
        $getMessage = function ($record) { return $record['message']; };
        $logs = array_map($getMessage, $this->handler->getRecords());
        $this->assertSame($expected, $logs);
    }
}
