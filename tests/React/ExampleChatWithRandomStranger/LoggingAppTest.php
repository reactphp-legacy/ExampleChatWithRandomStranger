<?php

namespace React\ExampleChatWithRandomStranger;

use Monolog\Logger;
use Monolog\Handler\TestHandler;

class LoggingAppTest extends \PHPUnit_Framework_TestCase
{
    private $handler;
    private $logger;
    private $appMock;
    private $app;

    public function setUp()
    {
        $this->handler = new TestHandler();

        $this->logger = new Logger('ExampleChatWithRandomStranger');
        $this->logger->pushHandler($this->handler);

        $this->appMock = $this->getMock('React\ExampleChatWithRandomStranger\AppInterface');

        $this->app = new LoggingApp(
            $this->appMock,
            $this->logger
        );
    }

    /** @test */
    public function connectShouldDelegateToDecoratedApp()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $this->appMock
            ->expects($this->once())
            ->method('connect')
            ->with($alice);

        $this->app->connect($alice);
    }

    /** @test */
    public function connectShouldLogNewConnection()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $this->app->connect($alice);

        $this->assertLogs([
            'New connection Alice',
        ]);
    }

    /** @test */
    public function disconnectShouldLogDisconnection()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $this->app->connect($alice);

        $alice->close();

        $this->assertLogs([
            'New connection Alice',
            'Connection Alice disconnected',
        ]);
    }

    /** @test */
    public function connectingTwoUsersShouldLogPipingOnce()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $bob = new ConnectionStub();
        $bob->id = 'Bob';

        $this->app->connect($alice);
        $this->app->connect($bob);

        $alice->pipe($bob)->pipe($alice);

        $this->assertLogs([
            'New connection Alice',
            'New connection Bob',
            'Pairing up connection Alice with waiting connection Bob',
        ]);
    }

    private function assertLogs($expected)
    {
        $getMessage = function ($record) { return $record['message']; };
        $logs = array_map($getMessage, $this->handler->getRecords());
        $this->assertSame($expected, $logs);
    }
}
