<?php

namespace React\ExampleChatWithRandomStranger;

use React\Socket\ConnectionInterface;
use Monolog\Logger;

class LoggingApp implements AppInterface
{
    private $app;
    private $logger;

    public function __construct(AppInterface $app, Logger $logger)
    {
        $this->app = $app;
        $this->logger = $logger;
    }

    public function connect(ConnectionInterface $conn)
    {
        $this->logger->info(sprintf("New connection %s", $conn->id));

        $conn->on('end', function () use ($conn) {
            $this->logger->info(sprintf("Connection %s disconnected", $conn->id));
        });

        $conn->on('pipe', function ($source) use ($conn) {
            if (!empty($conn->pipeLogged) || !empty($source->pipeLogged)) {
                return;
            }

            $this->logger->info(sprintf("Pairing up connection %s with waiting connection %s",
                $source->id, $conn->id));

            $conn->pipeLogged = $conn->pipeLogged = true;
        });

        $this->app->connect($conn);
    }
}
