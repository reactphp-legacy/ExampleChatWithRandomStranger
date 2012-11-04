<?php

namespace React\Chatroulette;

use React\Socket\ConnectionInterface;

class TextAppTest extends \PHPUnit_Framework_TestCase
{
    private $appMock;
    private $app;

    public function setUp()
    {
        $this->appMock = $this->getMock('React\Chatroulette\AppInterface');
        $this->app = new TextApp($this->appMock);
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
    public function connectShouldGreetUser()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $this->app->connect($alice);

        $this->assertConnectionData($alice, [
            'Hello Alice!',
        ]);
    }

    /** @test */
    public function connectionWaitEventShouldTriggerWaitMessage()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $this->app->connect($alice);

        $alice->emit('wait');

        $this->assertConnectionData($alice, [
            'Hello Alice!',
            'Please wait until a partner connects.',
        ]);
    }

    /** @test */
    public function connectionPipeShouldTriggerTalkingToMessage()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $bob = new ConnectionStub();
        $bob->id = 'Bob';

        $this->app->connect($alice);
        $this->app->connect($bob);

        $alice->pipe($bob)->pipe($alice);

        $this->assertConnectionData($alice, [
            'Hello Alice!',
            'You are now talking to Bob.',
        ]);

        $this->assertConnectionData($bob, [
            'Hello Bob!',
            'You are now talking to Alice.',
        ]);
    }

    private function assertConnectionData(ConnectionInterface $conn, $expected)
    {
        $data = implode("\n", $expected)."\n";
        $this->assertSame($data, $conn->data);
    }
}
