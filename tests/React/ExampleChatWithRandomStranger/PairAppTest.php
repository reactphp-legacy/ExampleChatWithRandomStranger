<?php

namespace React\ExampleChatWithRandomStranger;
use React\Socket\ConnectionInterface;

class PairAppTest extends \PHPUnit_Framework_TestCase
{
    private $app;

    public function setUp()
    {
        $this->app = new PairApp();
    }

    /** @test */
    public function connectingOneUserShouldMakeHimWait()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';
        $alice->on('wait', $this->expectCallableOnce());

        $this->app->connect($alice);
    }

    /** @test */
    public function connectingTwoUsersShouldPairThemUp()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';
        $alice->on('pipe', $this->expectCallableOnce());

        $bob = new ConnectionStub();
        $bob->id = 'Bob';
        $bob->on('pipe', $this->expectCallableOnce());

        $this->app->connect($alice);
        $this->app->connect($bob);
    }

    /** @test */
    public function connectingThreeUsersShouldMakeTheThirdOneWait()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $bob = new ConnectionStub();
        $bob->id = 'Bob';

        $carol = new ConnectionStub();
        $carol->id = 'Carol';
        $carol->on('wait', $this->expectCallableOnce());

        $this->app->connect($alice);
        $this->app->connect($bob);
        $this->app->connect($carol);
    }

    /** @test */
    public function connectingFourUsersShouldPairThemUpInPairs()
    {
        $alice = new ConnectionStub();
        $alice->id = 'Alice';

        $bob = new ConnectionStub();
        $bob->id = 'Bob';

        $carol = new ConnectionStub();
        $carol->id = 'Carol';
        $carol->on('pipe', $this->expectCallableOnce());

        $dan = new ConnectionStub();
        $dan->id = 'Dan';
        $dan->on('pipe', $this->expectCallableOnce());

        $this->app->connect($alice);
        $this->app->connect($bob);
        $this->app->connect($carol);
        $this->app->connect($dan);
    }

    protected function expectCallableOnce()
    {
        $mock = $this->createCallableMock();
        $mock
            ->expects($this->once())
            ->method('__invoke');

        return $mock;
    }

    protected function createCallableMock()
    {
        return $this->getMock('React\ExampleChatWithRandomStranger\CallableStub');
    }
}
