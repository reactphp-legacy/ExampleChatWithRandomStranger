<?php

namespace React\Chatroulette;

use Monolog\Logger;
use React\Socket\ConnectionInterface;

class Chatroulette
{
    private $logger;
    private $waiting;

    public function __construct(Logger $logger)
    {
        $this->logger = $logger;
    }

    public function connect(ConnectionInterface $conn)
    {
        $waiting = $this->waiting;

        $this->logger->info(sprintf("New connection %s", $conn->id));
        $conn->write(sprintf("Hello %s!\n", $conn->id));

        $conn->on('end', function () use ($conn) {
            $this->logger->info(sprintf("Connection %s disconnected", $conn->id));
        });

        if (null === $waiting || !$waiting->isReadable()) {
            $this->waiting = $conn;
            $conn->write("Please wait until a partner connects.\n");
            return;
        }

        $this->logger->info(sprintf("Pairing up connection %s with waiting connection %s",
            $conn->id, $waiting->id));

        $message = "You are now talking to %s.\n";
        $conn->write(sprintf($message, $waiting->id));
        $waiting->write(sprintf($message, $conn->id));

        $conn->pipe($waiting)->pipe($conn);

        $this->waiting = null;
    }
}
