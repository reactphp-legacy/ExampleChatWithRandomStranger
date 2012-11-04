<?php

namespace React\Chatroulette;

use React\Socket\ConnectionInterface;

class PairApp implements AppInterface
{
    private $waiting;

    public function connect(ConnectionInterface $conn)
    {
        $waiting = $this->waiting;

        if (null === $waiting || !$waiting->isReadable()) {
            $this->waiting = $conn;
            $conn->emit('wait');
            return;
        }

        $conn->pipe($waiting)->pipe($conn);

        $this->waiting = null;
    }
}
