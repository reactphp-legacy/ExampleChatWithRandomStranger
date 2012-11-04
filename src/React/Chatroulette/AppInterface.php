<?php

namespace React\Chatroulette;

use React\Socket\ConnectionInterface;

interface AppInterface
{
    function connect(ConnectionInterface $conn);
}
