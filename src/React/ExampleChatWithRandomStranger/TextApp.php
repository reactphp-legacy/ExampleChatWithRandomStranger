<?php

namespace React\ExampleChatWithRandomStranger;

use React\Socket\ConnectionInterface;

class TextApp implements AppInterface
{
    private $app;

    public function __construct(AppInterface $app)
    {
        $this->app = $app;
    }

    public function connect(ConnectionInterface $conn)
    {
        $conn->write(sprintf("Hello %s!\n", $conn->id));

        $conn->on('wait', function () use ($conn) {
            $conn->write("Please wait until a partner connects.\n");
        });

        $conn->on('pipe', function ($source) use ($conn) {
            $message = "You are now talking to %s.\n";
            $conn->write(sprintf($message, $source->id));
        });

        $this->app->connect($conn);
    }
}
